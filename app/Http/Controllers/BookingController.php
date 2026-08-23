<?php

namespace App\Http\Controllers;

use App\DataTables\BookingsDataTable;
use App\Http\Requests\BookingRequest;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\BookingInfoEquipment;
use App\Models\Carrier;
use App\Models\Commodity;
use App\Models\ContainerSize;
use App\Models\ContainerType;
use App\Models\Party;
use App\Models\Pod;
use App\Models\Pol;
use App\Models\ShipperBp;
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
        return view('bookings.create', $this->formData());
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

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
        $booking->load('otherInfo', 'equipments.containerSize', 'equipments.containerType', 'revenues', 'costs', 'polPol', 'podPofd', 'polPot1', 'polPot2', 'agentPol', 'agentPofd', 'agent1', 'agent2', 'shipperBp', 'vesselVoyage', 'bookingCommodity');

        return view('bookings.edit', array_merge(['booking' => $booking], $this->formData()));
    }

    public function update(BookingRequest $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validated();

        $otherInfoData = collect($validated)->only([
            'special_req', 'free_days_pol', 'detention_free_pofd',
            'detention_tariff', 'detention_currency', 'message',
        ])->toArray();

        $booking->update(collect($validated)->except(array_keys($otherInfoData))->toArray());
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

    private function formData(): array
    {
        return [
            'carriers' => Carrier::orderBy('name')->get(),
            'commodities' => Commodity::orderBy('commodity_number')->get(),
            'vesselVoyages' => VesselVoyage::orderBy('vessel_name')->get(),
            'pols' => Pol::orderBy('city')->get(),
            'pofds' => Pod::orderBy('city')->get(),
            'agents' => Agent::orderBy('name')->get(),
            'shipperBps' => ShipperBp::orderBy('name')->get(),
            'parties' => Party::orderBy('name')->get(),
            'containerSizes' => ContainerSize::orderBy('size')->get(),
            'containerTypes' => ContainerType::orderBy('name')->get(),
            'cntrOwners' => config('dropdowns.bookings.cntr_owner'),
            'freightTypes' => config('dropdowns.bookings.freight_type'),
            'freightTypeSubs' => config('dropdowns.bookings.freight_type_sub'),
            'nonDgs' => config('dropdowns.bookings.non_dg'),
            'currencies' => config('dropdowns.bookings.detention_currency'),
        ];
    }
}
