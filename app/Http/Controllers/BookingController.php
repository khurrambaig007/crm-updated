<?php

namespace App\Http\Controllers;

use App\DataTables\BookingsDataTable;
use App\Http\Requests\BookingRequest;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\BookingCost;
use App\Models\BookingInfoEquipment;
use App\Models\BookingRevenue;
use App\Models\Carrier;
use App\Models\Charge;
use App\Models\Commodity;
use App\Models\ContainerSize;
use App\Models\ContainerType;
use App\Models\Currency;
use App\Models\FreightType;
use App\Models\Party;
use App\Models\Pod;
use App\Models\Pol;
use App\Models\ShipperBp;
use App\Models\SlotTerm;
use App\Models\VesselVoyage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request, BookingsDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('bookings.index');
    }

    public function create(): View
    {
        return view('bookings.create', array_merge($this->formData(), [
            'bookingPrefix' => (string) array_key_first(config('dropdowns.bookings.booking_prefix')),
            'bookingNoSeq' => str_pad((string) $this->nextBookingSeq(), 6, '0', STR_PAD_LEFT),
            'bookingNoPreview' => $this->nextBookingNo(null, null),
        ]));
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['booking_no'] = $this->nextBookingNo($validated['pol'] ?? null, $validated['pofd'] ?? null);
        $validated['reporting_no'] = $this->nextReportingNo();

        $otherInfoData = collect($validated)->only([
            'special_req', 'free_days_pol', 'detention_free_pofd',
            'detention_tariff', 'detention_currency', 'message',
        ])->toArray();

        $booking = Booking::create(collect($validated)->except(array_keys($otherInfoData))->toArray());
        $booking->otherInfo()->create($otherInfoData);

        return redirect()->route('bookings.index')->with('status', 'Booking created successfully.');
    }

    public function edit(Booking $booking): View
    {
        $booking->load('otherInfo', 'equipments.containerSize', 'equipments.containerType', 'revenues.charge', 'revenues.containerSize', 'revenues.containerType', 'costs.charge', 'costs.containerSize', 'costs.containerType', 'costs.slotTerm', 'polPol', 'podPofd', 'polPot1', 'polPot2', 'agentPol', 'agentPofd', 'agent1', 'agent2', 'shipperBp', 'vesselVoyage', 'bookingCommodity');

        return view('bookings.edit', array_merge(['booking' => $booking], $this->formData()));
    }

    public function update(BookingRequest $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validated();

        $otherInfoData = collect($validated)->only([
            'special_req', 'free_days_pol', 'detention_free_pofd',
            'detention_tariff', 'detention_currency', 'message',
        ])->toArray();

        $booking->update(collect($validated)->except(array_merge(['booking_no', 'reporting_no'], array_keys($otherInfoData)))->toArray());
        $booking->otherInfo()->updateOrCreate(['booking_id' => $booking->id], $otherInfoData);

        return redirect()->route('bookings.index')->with('status', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()->route('bookings.index')->with('status', 'Booking deleted successfully.');
    }

    public function updateOtherInfo(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'special_req' => ['nullable', 'string'],
            'free_days_pol' => ['nullable', 'integer'],
            'detention_free_pofd' => ['nullable', 'integer'],
            'detention_tariff' => ['boolean'],
            'detention_currency' => ['nullable', 'string', 'max:191'],
        ]);

        $booking->otherInfo()->updateOrCreate(['booking_id' => $booking->id], $validated);

        return redirect()->route('bookings.edit', ['booking' => $booking, 'tab' => 'other-info'])->with('status', 'Other info updated successfully.');
    }

    public function updateMessage(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string'],
        ]);

        $booking->otherInfo()->updateOrCreate(['booking_id' => $booking->id], $validated);

        return redirect()->route('bookings.edit', ['booking' => $booking, 'tab' => 'message'])->with('status', 'Message updated successfully.');
    }

    public function storeEquipment(Request $request, Booking $booking): JsonResponse
    {
        $validated = $this->validateEquipment($request);

        $equipment = $booking->equipments()->create($validated);
        $equipment->load('containerSize', 'containerType');

        return response()->json([
            'message' => 'Equipment saved successfully.',
            'row' => $this->equipmentRow($equipment),
        ], 201);
    }

    public function updateEquipment(Request $request, Booking $booking, BookingInfoEquipment $equipment): JsonResponse
    {
        abort_unless($equipment->booking_id === $booking->id, 404);

        $validated = $this->validateEquipment($request);

        $equipment->update($validated);
        $equipment->refresh();
        $equipment->load('containerSize', 'containerType');

        return response()->json([
            'message' => 'Equipment updated successfully.',
            'row' => $this->equipmentRow($equipment),
        ]);
    }

    public function destroyEquipment(Booking $booking, BookingInfoEquipment $equipment): JsonResponse
    {
        abort_unless($equipment->booking_id === $booking->id, 404);

        $equipment->delete();

        return response()->json([
            'message' => 'Equipment deleted successfully.',
        ]);
    }

    private function validateEquipment(Request $request): array
    {
        return $request->validate([
            'size' => ['required', 'integer', 'exists:container_sizes,id'],
            'type' => ['required', 'integer', 'exists:container_types,id'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'approval_status' => ['required', 'integer', 'in:'.implode(',', array_keys(config('dropdowns.bookings.approval_status')))],
            'gross_weight' => ['nullable', 'string', 'max:191'],
            'packages' => ['nullable', 'string', 'max:191'],
            'unit' => ['nullable', 'integer'],
            'cargo_volumn' => ['nullable', 'string', 'max:191'],
        ]);
    }

    private function equipmentRow(BookingInfoEquipment $equipment): array
    {
        return [
            'id' => $equipment->id,
            'size_id' => $equipment->size,
            'type_id' => $equipment->type,
            'quantity' => $equipment->quantity,
            'approval_status' => $equipment->approval_status,
            'gross_weight' => $equipment->gross_weight,
            'packages' => $equipment->packages,
            'unit' => $equipment->unit,
            'cargo_volumn' => $equipment->cargo_volumn,
            'size' => $equipment->containerSize?->size,
            'type' => $equipment->containerType?->name,
        ];
    }

    public function storeRevenue(Request $request, Booking $booking): JsonResponse
    {
        $validated = $this->validateRevenue($request);

        $revenue = $booking->revenues()->create($validated);
        $revenue->load('charge', 'containerSize', 'containerType');

        return response()->json([
            'message' => 'Revenue saved successfully.',
            'row' => $this->revenueRow($revenue),
        ], 201);
    }

    public function updateRevenue(Request $request, Booking $booking, BookingRevenue $revenue): JsonResponse
    {
        abort_unless($revenue->booking_id === $booking->id, 404);

        $validated = $this->validateRevenue($request);

        $revenue->update($validated);
        $revenue->refresh();
        $revenue->load('charge', 'containerSize', 'containerType');

        return response()->json([
            'message' => 'Revenue updated successfully.',
            'row' => $this->revenueRow($revenue),
        ]);
    }

    public function destroyRevenue(Booking $booking, BookingRevenue $revenue): JsonResponse
    {
        abort_unless($revenue->booking_id === $booking->id, 404);

        $revenue->delete();

        return response()->json([
            'message' => 'Revenue deleted successfully.',
        ]);
    }

    private function validateRevenue(Request $request): array
    {
        $currencyKeys = implode(',', array_keys(config('dropdowns.bookings.detention_currency')));

        return $request->validate([
            'charge_id' => ['required', 'integer', 'exists:charges,id'],
            'container_size_id' => ['required', 'integer', 'exists:container_sizes,id'],
            'container_type_id' => ['required', 'integer', 'exists:container_types,id'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'mrg' => ['nullable', 'numeric'],
            'rate' => ['nullable', 'numeric'],
            'amount' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'in:'.$currencyKeys],
            'ex_rate' => ['nullable', 'numeric'],
            'amount_in_dollar' => ['nullable', 'numeric'],
            'freight_type' => ['nullable', 'string', 'max:191'],
            'pa_party_tpa_agent' => ['nullable', 'string'],
            'hide' => ['boolean'],
            'remarks' => ['nullable', 'string'],
        ]);
    }

    private function revenueRow(BookingRevenue $revenue): array
    {
        return [
            'id' => $revenue->id,
            'charges' => $revenue->charge?->name,
            'size' => $revenue->containerSize?->size,
            'type' => $revenue->containerType?->name,
            'quantity' => $revenue->quantity,
            'mrg' => $revenue->mrg,
            'rate' => $revenue->rate,
            'amount' => $revenue->amount,
            'currency' => $revenue->currency,
            'ex_rate' => $revenue->ex_rate,
            'amount_in_dollar' => $revenue->amount_in_dollar,
            'freight_type' => $revenue->freight_type,
            'pa_party_tpa_agent' => $revenue->pa_party_tpa_agent,
            'hide' => (bool) $revenue->hide,
            'remarks' => $revenue->remarks,
        ];
    }

    public function storeCost(Request $request, Booking $booking): JsonResponse
    {
        $validated = $this->validateCost($request);

        $cost = $booking->costs()->create($validated);
        $cost->load('charge', 'containerSize', 'containerType', 'slotTerm');

        return response()->json([
            'message' => 'Cost saved successfully.',
            'row' => $this->costRow($cost),
        ], 201);
    }

    public function updateCost(Request $request, Booking $booking, BookingCost $cost): JsonResponse
    {
        abort_unless($cost->booking_id === $booking->id, 404);

        $validated = $this->validateCost($request);

        $cost->update($validated);
        $cost->refresh();
        $cost->load('charge', 'containerSize', 'containerType', 'slotTerm');

        return response()->json([
            'message' => 'Cost updated successfully.',
            'row' => $this->costRow($cost),
        ]);
    }

    public function destroyCost(Booking $booking, BookingCost $cost): JsonResponse
    {
        abort_unless($cost->booking_id === $booking->id, 404);

        $cost->delete();

        return response()->json([
            'message' => 'Cost deleted successfully.',
        ]);
    }

    private function validateCost(Request $request): array
    {
        $currencyKeys = implode(',', array_keys(config('dropdowns.bookings.detention_currency')));

        return $request->validate([
            'charge_id' => ['required', 'integer', 'exists:charges,id'],
            'container_size_id' => ['required', 'integer', 'exists:container_sizes,id'],
            'container_type_id' => ['required', 'integer', 'exists:container_types,id'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'mrg' => ['nullable', 'numeric'],
            'cost' => ['nullable', 'numeric'],
            'amount' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'in:'.$currencyKeys],
            'ex_rate' => ['nullable', 'numeric'],
            'amount_in_dollar' => ['nullable', 'numeric'],
            'freight_type' => ['nullable', 'string', 'max:191'],
            'pa_party_tpa_agent' => ['nullable', 'string'],
            'slot_term' => ['nullable', 'integer', 'exists:slot_terms,id'],
            'hide' => ['boolean'],
            'remarks' => ['nullable', 'string'],
        ]);
    }

    private function costRow(BookingCost $cost): array
    {
        return [
            'id' => $cost->id,
            'charges' => $cost->charge?->name,
            'size' => $cost->containerSize?->size,
            'type' => $cost->containerType?->name,
            'quantity' => $cost->quantity,
            'mrg' => $cost->mrg,
            'cost' => $cost->cost,
            'amount' => $cost->amount,
            'currency' => $cost->currency,
            'ex_rate' => $cost->ex_rate,
            'amount_in_dollar' => $cost->amount_in_dollar,
            'freight_type' => $cost->freight_type,
            'pa_party_tpa_agent' => $cost->pa_party_tpa_agent,
            'slot_term' => $cost->slotTerm?->term,
            'hide' => (bool) $cost->hide,
            'remarks' => $cost->remarks,
        ];
    }

    public function approve(Request $request, Booking $booking): JsonResponse
    {
        $booking->update([
            'approved' => ! $booking->approved,
        ]);

        return response()->json([
            'approved' => $booking->approved,
            'message' => $booking->approved ? 'Booking approved.' : 'Booking unapproved.',
        ]);
    }

    private function nextBookingSeq(): int
    {
        return (Booking::withTrashed()->pluck('booking_no')
            ->map(fn (?string $no) => (int) preg_replace('/\D/', '', (string) $no))
            ->max() ?? 0) + 1;
    }

    private function nextBookingNo(?int $polId, ?int $pofdId): string
    {
        $prefix = (string) array_key_first(config('dropdowns.bookings.booking_prefix'));
        $polCode = $polId ? Pol::query()->whereKey($polId)->value('port_code') : null;
        $pofdCode = $pofdId ? Pod::query()->whereKey($pofdId)->value('location_code') : null;

        return $prefix.strtoupper((string) $polCode).strtoupper((string) $pofdCode)
            .str_pad((string) $this->nextBookingSeq(), 6, '0', STR_PAD_LEFT);
    }

    private function nextReportingNo(): string
    {
        $prefix = (string) array_key_first(config('dropdowns.bookings.reporting_prefix'));
        $year = now()->format('y');

        $count = Booking::withTrashed()
            ->where('reporting_no', 'like', $prefix.'-%/'.$year)
            ->count();

        return $prefix.'-'.($count + 1).'/'.$year;
    }

    private function formData(): array
    {
        $currency = Currency::orderBy('exchange_rate_date', 'desc')->first();
        $rates = $currency ? (json_decode($currency->exchange_rate ?? '', true) ?: []) : [];
        $currencies = ! empty($rates)
            ? array_combine(array_keys($rates), array_keys($rates))
            : config('dropdowns.bookings.detention_currency');

        return [
            'carriers' => Carrier::orderBy('name')->get(),
            'commodities' => Commodity::orderBy('commodity_number')->get(),
            'vesselVoyages' => VesselVoyage::orderBy('vessel_name')->get(),
            'pols' => Pol::orderBy('city')->get(),
            'pofds' => Pod::orderBy('city')->get(),
            'agents' => Agent::orderBy('name')->get(),
            'shipperBps' => ShipperBp::orderBy('name')->get(),
            'parties' => Party::orderBy('name')->get(),
            'revenueFreightTypes' => FreightType::orderBy('name')->pluck('name')->all(),
            'slotTerms' => SlotTerm::orderBy('term')->get()->map(fn ($s) => ['id' => $s->id, 'label' => $s->term])->values()->all(),
            'containerSizes' => ContainerSize::orderBy('size')->get(),
            'containerTypes' => ContainerType::orderBy('name')->get(),
            'charges' => Charge::orderBy('name')->get(),
            'cntrOwners' => config('dropdowns.bookings.cntr_owner'),
            'freightTypes' => config('dropdowns.bookings.freight_type'),
            'freightTypeSubs' => config('dropdowns.bookings.freight_type_sub'),
            'nonDgs' => config('dropdowns.bookings.non_dg'),
            'currencies' => $currencies,
            'rates' => $rates,
            'exchangeRateDate' => $currency?->exchange_rate_date?->format('Y-m-d'),
        ];
    }
}
