<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlInfo\UpdateBlDetailRequest;
use App\Http\Requests\BlInfo\UpdateBlInfoRequest;
use App\Http\Requests\BlInfo\UpdateBookingInfoRequest;
use App\Http\Requests\BlInfo\UpdateDeliveryOrderRequest;
use App\Http\Requests\BlInfo\UpdateLockInfoRequest;
use App\Http\Requests\BlInfo\UpdateReleaseInstructionsRequest;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\BookingBlDetail;
use App\Models\Carrier;
use App\Models\Party;
use App\Models\Pod;
use App\Models\Pol;
use App\Models\ShipperBp;
use App\Models\VesselVoyage;
use App\Services\BlDetailPdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
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
            view: 'bl-info.booking-info',
        );
    }

    public function releaseInstructions(Booking $booking): View
    {
        return $this->render(
            $booking,
            'release-instructions',
            'Release Instructions',
            'Release authorization details for this bill of lading.',
            view: 'bl-info.release-instructions',
        );
    }

    public function deliveryOrder(Booking $booking): View
    {
        return $this->render(
            $booking,
            'delivery-order',
            'Delivery Order',
            'Delivery order details and the party the cargo is released to.',
            view: 'bl-info.delivery-order',
        );
    }

    public function lockInfo(Booking $booking): View
    {
        return $this->render(
            $booking,
            'lock-info',
            'Lock Info',
            'Where and when each leg of the routing was locked.',
            view: 'bl-info.lock-info',
        );
    }

    public function blDetail(Booking $booking): View
    {
        return $this->render(
            $booking,
            'bl-detail',
            'BL Detail',
            'Cargo particulars as they appear on the bill of lading.',
            view: 'bl-info.bl-detail',
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

    public function updateReleaseInstructions(UpdateReleaseInstructionsRequest $request, Booking $booking): RedirectResponse
    {
        $this->detailFor($booking)->update($request->validated());

        return redirect()->route('bl-info.release-instructions', $booking)
            ->with('status', 'Release Instructions updated successfully.');
    }

    public function updateDeliveryOrder(UpdateDeliveryOrderRequest $request, Booking $booking): RedirectResponse
    {
        $this->detailFor($booking)->update($request->validated());

        return redirect()->route('bl-info.delivery-order', $booking)
            ->with('status', 'Delivery Order updated successfully.');
    }

    public function updateLockInfo(UpdateLockInfoRequest $request, Booking $booking): RedirectResponse
    {
        $this->detailFor($booking)->update($request->validated());

        return redirect()->route('bl-info.lock-info', $booking)
            ->with('status', 'Lock Info updated successfully.');
    }

    public function updateBlDetail(UpdateBlDetailRequest $request, Booking $booking): RedirectResponse
    {
        $this->detailFor($booking)->update($request->validated());

        return redirect()->route('bl-info.bl-detail', $booking)
            ->with('status', 'BL Detail updated successfully.');
    }

    public function viewBlDetailPdf(Booking $booking): Response
    {
        // Inline disposition renders the PDF in the browser instead of downloading it.
        return response($this->buildBlDetailPdf($booking), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="BL-'.$booking->booking_no.'.pdf"',
        ]);
    }

    public function downloadBlDetailPdf(Booking $booking): Response
    {
        return response($this->buildBlDetailPdf($booking), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="BL-'.$booking->booking_no.'.pdf"',
        ]);
    }

    private function buildBlDetailPdf(Booking $booking): string
    {
        $detail = $this->detailFor($booking);

        return (new BlDetailPdf(resource_path('pdf/bl-detail-template.pdf')))->render($detail);
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
            'carriers' => Carrier::orderBy('name')->get(),
            'releaseStatuses' => config('dropdowns.bl_info.release_status'),
            'releaseTypes' => config('dropdowns.bl_info.release_type'),
            'units' => config('dropdowns.bl_info.units'),
            'freightTypes' => config('dropdowns.bookings.freight_type'),
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

        $detail = $booking->blDetail()->firstOrCreate([], [
            'bl_info_booking_no' => $booking->booking_no,
            'bl_info_sailing_date' => $booking->sailing_date?->format('Y-m-d'),
            'bl_info_bl_number' => BookingBlDetail::nextBlNumber(),
        ]);

        $this->backfillFromBooking($detail, $booking);

        return $detail;
    }

    /**
     * Fill any empty BL Info fields from the booking header. This heals records
     * created before a field was seeded (or lazily created by a direct visit)
     * without ever overwriting values the user has entered.
     */
    private function backfillFromBooking(BookingBlDetail $detail, Booking $booking): void
    {
        $fill = [];

        foreach (BookingBlDetail::fromBooking($booking) as $column => $value) {
            if ($detail->{$column} === null && $value !== null) {
                $fill[$column] = $value;
            }
        }

        if ($fill !== []) {
            $detail->update($fill);
        }
    }
}
