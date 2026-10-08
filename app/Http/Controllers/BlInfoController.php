<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlInfo\UpdateBlInfoRequest;
use App\Http\Requests\BlInfo\UpdateBookingInfoRequest;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\BookingBlDetail;
use App\Models\Party;
use App\Models\Pod;
use App\Models\Pol;
use App\Models\ShipperBp;
use App\Models\VesselVoyage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BlInfoController extends Controller
{
    public function index(Booking $booking): View
    {
        return $this->render($booking, 'index', 'Bl Info', 'Capture the bill of lading header for this approved booking.');
    }

    public function bookingInfo(Booking $booking): View
    {
        return $this->render(
            $booking,
            'booking-info',
            'Booking Info',
            'Routing, parties and references carried over from the booking.',
            'bl-info.booking-info',
        );
    }

    public function releaseInstructions(Booking $booking): View
    {
        return $this->render(
            $booking,
            'release-instructions',
            'Release Instructions',
            'Release authorization details for this bill of lading.',
            placeholder: true,
        );
    }

    public function deliveryOrder(Booking $booking): View
    {
        return $this->render(
            $booking,
            'delivery-order',
            'Delivery Order',
            'Delivery order details and the party the cargo is released to.',
            placeholder: true,
        );
    }

    public function lockInfo(Booking $booking): View
    {
        return $this->render(
            $booking,
            'lock-info',
            'Lock Info',
            'Where and when each leg of the routing was locked.',
            placeholder: true,
        );
    }

    public function authorization(Booking $booking): View
    {
        return $this->render(
            $booking,
            'authorization',
            'Authorization',
            'Approvals and sign-off for this bill of lading.',
            placeholder: true,
        );
    }

    public function update(UpdateBlInfoRequest $request, Booking $booking): RedirectResponse
    {
        $this->detailFor($booking)->update($request->validated());

        return redirect()->route('bl-info.index', $booking)
            ->with('status', 'BL Info updated successfully.');
    }

    public function updateBookingInfo(UpdateBookingInfoRequest $request, Booking $booking): RedirectResponse
    {
        $this->detailFor($booking)->update($request->validated());

        return redirect()->route('bl-info.booking-info', $booking)
            ->with('status', 'Booking Info updated successfully.');
    }

    private function render(
        Booking $booking,
        string $activeTab,
        string $title,
        string $description,
        ?string $view = null,
        bool $placeholder = false,
    ): View {
        $detail = $this->detailFor($booking);

        return view($view ?? ($placeholder ? 'bl-info.placeholder' : 'bl-info.index'), array_merge([
            'booking' => $booking,
            'blDetail' => $detail,
            'activeTab' => $activeTab,
            'tabTitle' => $title,
            'tabDescription' => $description,
        ], $this->formData()));
    }

    /**
     * Lookup lists shared by the BL Info and Booking Info tabs.
     *
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'agents' => Agent::orderBy('name')->get(),
            'vesselVoyages' => VesselVoyage::orderBy('vessel_name')->get(),
            'siStatuses' => config('dropdowns.bl_info.booking_si_status'),
            'pols' => Pol::orderBy('city')->get(),
            'pofds' => Pod::orderBy('city')->get(),
            'shipperBps' => ShipperBp::orderBy('name')->get(),
            'parties' => Party::orderBy('name')->get(),
            'carriers' => config('dropdowns.bl_info.booking_info_carrier'),
        ];
    }

    /**
     * The BL record is created when a booking is approved. Resolve it lazily as
     * a safety net, but only ever for an approved booking — the URL is the only
     * entry point, so an unapproved booking must not expose the screen.
     */
    private function detailFor(Booking $booking): BookingBlDetail
    {
        abort_unless($booking->approved, 404);

        return $booking->blDetail()->firstOrCreate([], [
            'bl_info_booking_no' => $booking->booking_no,
            'bl_info_sailing_date' => $booking->sailing_date?->format('Y-m-d'),
            'bl_info_bl_number' => BookingBlDetail::nextBlNumber(),
        ]);
    }
}
