<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingInfoEquipment;
use App\Models\Pod;
use App\Models\Pol;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingSplitService
{
    /**
     * Number of decimals equipment quantities are compared at. The column is a
     * double, so raw float comparison can let 0.1+0.2 fail a "less than or equal"
     * check that should pass.
     */
    private const QUANTITY_PRECISION = 2;

    /**
     * Booking columns that must never be copied verbatim onto a split child:
     * identity (booking_no/reporting_no/parent_booking_id), lifecycle flags
     * (booking_status/is_split_booking/approved) and the soft-delete column.
     *
     * @var list<string>
     */
    private const NON_INHERITED = [
        'id',
        'booking_no',
        'reporting_no',
        'booking_status',
        'is_split_booking',
        'parent_booking_id',
        'approved',
        'deleted_at',
    ];

    /**
     * Equipment rows aggregated by (size, type) for the split screen.
     *
     * A booking may legitimately hold more than one row for the same size and
     * type — the (booking_id, size, type) index is not unique — so the split UI
     * and its validation both work against the grouped total.
     *
     * @return Collection<int, array{key: string, size: int, type: int, size_label: string, type_label: string, quantity: float, rows: Collection<int, BookingInfoEquipment>}>
     */
    public function equipmentSummary(Booking $booking): Collection
    {
        return $booking->equipments()
            ->with(['containerSize', 'containerType'])
            ->orderBy('size')
            ->orderBy('type')
            ->orderBy('id')
            ->get()
            ->groupBy(fn (BookingInfoEquipment $e) => $e->size.'-'.$e->type)
            ->map(function (Collection $rows, string $key): array {
                /** @var BookingInfoEquipment $first */
                $first = $rows->first();

                return [
                    'key' => $key,
                    'size' => (int) $first->size,
                    'type' => (int) $first->type,
                    'size_label' => $first->containerSize?->size ?? (string) $first->size,
                    'type_label' => $first->containerType?->name ?? (string) $first->type,
                    'quantity' => $this->round((float) $rows->sum('quantity')),
                    'rows' => $rows->values(),
                ];
            })
            ->values();
    }

    /**
     * Next split number for a parent, e.g. AMSSHAKHI000001-01, then -02.
     * Chained splits append to the immediate parent: AMSSHAKHI000001-01-01.
     */
    public function nextSplitBookingNo(Booking $parent): string
    {
        $used = $parent->splitBookings()
            ->pluck('booking_no')
            ->map(fn (?string $no) => (int) preg_replace('/\D/', '', substr((string) $no, strrpos((string) $no, '-') + 1)))
            ->all();

        $sequence = 1;
        while (in_array($sequence, $used, true)) {
            $sequence++;
        }

        return $parent->booking_no.'-'.str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Global booking sequence. Split children are skipped: a child number
     * carries a trailing "-NN" group, and stripping every non-digit would turn
     * AMSSHAKHI000001-01 into 100001 and push all later bookings up by 100000.
     */
    public function nextBookingSeq(): int
    {
        return (Booking::withTrashed()->pluck('booking_no')
            ->map(fn (?string $no) => (int) preg_replace('/\D/', '', (string) preg_replace('/(?:-\d+)+$/', '', (string) $no)))
            ->max() ?? 0) + 1;
    }

    /**
     * {prefix}{POL port_code}{POFD location_code}{6-digit padded global seq}
     */
    public function nextBookingNo(?int $polId, ?int $pofdId): string
    {
        $prefix = (string) array_key_first(config('dropdowns.bookings.booking_prefix'));
        $polCode = $polId ? Pol::query()->whereKey($polId)->value('port_code') : null;
        $pofdCode = $pofdId ? Pod::query()->whereKey($pofdId)->value('location_code') : null;

        return $prefix.strtoupper((string) $polCode).strtoupper((string) $pofdCode)
            .str_pad((string) $this->nextBookingSeq(), 6, '0', STR_PAD_LEFT);
    }

    public function nextReportingNo(): string
    {
        $prefix = (string) array_key_first(config('dropdowns.bookings.reporting_prefix'));
        $year = now()->format('y');

        $count = Booking::withTrashed()
            ->where('reporting_no', 'like', $prefix.'-%/'.$year)
            ->count();

        return $prefix.'-'.($count + 1).'/'.$year;
    }

    /**
     * Validate the requested split quantities against the equipment the booking
     * actually holds right now. Throws on the first violation.
     *
     * @param  array<int, array{size?: mixed, type?: mixed, quantity?: mixed}>  $requested
     * @param  array<string, float>|null  $locked  Pre-read group totals, to avoid re-querying.
     */
    public function assertSplitIsPossible(Booking $booking, array $requested, ?array $locked = null): void
    {
        $available = $locked ?? $this->availableByKey($this->equipmentSummary($booking));

        /** @var array<string, float> $requestedByKey */
        $requestedByKey = [];
        foreach ($requested as $row) {
            $key = ((int) ($row['size'] ?? 0)).'-'.((int) ($row['type'] ?? 0));
            $requestedByKey[$key] = ($requestedByKey[$key] ?? 0) + $this->round((float) ($row['quantity'] ?? 0));
        }

        foreach ($requestedByKey as $key => $quantity) {
            if ($quantity <= 0) {
                continue;
            }

            if (! array_key_exists($key, $available)) {
                throw ValidationException::withMessages([
                    'equipment' => 'This booking has no equipment of that type to split.',
                ]);
            }

            if ($quantity > $available[$key] + (1 / (10 ** self::QUANTITY_PRECISION))) {
                $label = $this->labelForKey($booking, $key);

                throw ValidationException::withMessages([
                    'equipment' => "You cannot split {$quantity} of {$label} — the booking only holds {$available[$key]}.",
                ]);
            }
        }

        if (array_sum($requestedByKey) <= 0) {
            throw ValidationException::withMessages([
                'equipment' => 'Enter a split quantity greater than zero for at least one equipment type.',
            ]);
        }
    }

    /**
     * Move equipment from a booking into a new child booking.
     *
     * Everything runs in one transaction and the equipment is re-read under a
     * row lock, so a quantity edited in the equipment grid between rendering the
     * form and submitting it cannot be over-split.
     *
     * @param  array<int, array{size?: mixed, type?: mixed, quantity?: mixed, gross_weight?: mixed, packages?: mixed, unit?: mixed, cargo_volumn?: mixed, approval_status?: mixed}>  $requested
     * @param  array<string, mixed>  $bookingAttributes  Overrides applied on top of the inherited header fields.
     */
    public function split(Booking $parent, array $requested, array $bookingAttributes = []): Booking
    {
        return DB::transaction(function () use ($parent, $requested, $bookingAttributes): Booking {
            $lockedRows = BookingInfoEquipment::query()
                ->where('booking_id', $parent->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $grouped = $lockedRows->groupBy(fn (BookingInfoEquipment $e) => $e->size.'-'.$e->type);

            $available = $grouped->map(fn (Collection $rows) => $this->round((float) $rows->sum('quantity')))->all();

            $this->assertSplitIsPossible($parent, $requested, $available);

            $child = $this->createChildBooking($parent, $bookingAttributes);
            $this->copyOtherInfo($parent, $child);

            /** @var array<string, array{quantity: float, row: array<string, mixed>}> $allocations */
            $allocations = [];

            foreach ($requested as $row) {
                $key = ((int) ($row['size'] ?? 0)).'-'.((int) ($row['type'] ?? 0));
                $quantity = $this->round((float) ($row['quantity'] ?? 0));

                if ($quantity <= 0) {
                    continue;
                }

                $taken = 0;
                foreach ($grouped->get($key, collect()) as $equipment) {
                    if ($taken >= $quantity) {
                        break;
                    }

                    $onHand = $this->round((float) $equipment->quantity);
                    $move = min($onHand, $this->round($quantity - $taken));

                    $equipment->quantity = $this->round($onHand - $move);

                    if ($equipment->quantity <= 0) {
                        $equipment->delete();
                    } else {
                        $equipment->save();
                    }

                    $taken = $this->round($taken + $move);
                }

                $allocations[$key] = ['quantity' => $quantity, 'row' => $row];
            }

            foreach ($allocations as $key => $allocation) {
                /** @var BookingInfoEquipment $source */
                $source = $grouped->get($key, collect())->first();
                $row = $allocation['row'];

                $child->equipments()->create([
                    'size' => $source->size,
                    'type' => $source->type,
                    'quantity' => $allocation['quantity'],
                    'gross_weight' => $this->stringOrNull($row['gross_weight'] ?? null),
                    'packages' => $this->stringOrNull($row['packages'] ?? null),
                    'unit' => isset($row['unit']) && $row['unit'] !== '' ? (int) $row['unit'] : null,
                    'cargo_volumn' => $this->stringOrNull($row['cargo_volumn'] ?? null),
                    'approval_status' => $row['approval_status'] ?? $source->approval_status,
                ]);
            }

            return $child->load('parentBooking');
        });
    }

    /**
     * @return array<string, float>
     */
    private function availableByKey(Collection $summary): array
    {
        return $summary->mapWithKeys(fn (array $group) => [$group['key'] => $group['quantity']])->all();
    }

    private function labelForKey(Booking $booking, string $key): string
    {
        $group = $this->equipmentSummary($booking)->firstWhere('key', $key);

        return $group ? $group['size_label'].' '.$group['type_label'] : 'that equipment type';
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createChildBooking(Booking $parent, array $attributes): Booking
    {
        $inherited = $parent->getAttributes();
        foreach (self::NON_INHERITED as $column) {
            unset($inherited[$column]);
        }

        $child = new Booking;
        $child->forceFill(array_merge($inherited, $attributes));
        $child->booking_no = $this->nextSplitBookingNo($parent);
        $child->reporting_no = $this->nextReportingNo();
        $child->is_split_booking = true;
        $child->parent_booking_id = $parent->id;
        $child->approved = false;
        $child->save();

        return $child;
    }

    private function copyOtherInfo(Booking $parent, Booking $child): void
    {
        $otherInfo = $parent->otherInfo;

        if ($otherInfo === null) {
            return;
        }

        $copy = $otherInfo->replicate(['booking_id']);
        $copy->booking_id = $child->id;
        $copy->save();
    }

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_scalar($value) ? (string) $value : null;
    }

    private function round(float $value): float
    {
        return round($value, self::QUANTITY_PRECISION);
    }
}
