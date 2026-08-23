<?php

namespace App\Http\Controllers;

use App\DataTables\BookingsDataTable;
use App\Http\Requests\BookingRequest;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\Carrier;
use App\Models\Commodity;
use App\Models\Party;
use App\Models\Pod;
use App\Models\Pol;
use App\Models\ShipperBp;
use App\Models\VesselVoyage;
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
        Booking::create($request->validated());

        return redirect()->route('bookings.index')->with('status', 'Booking created successfully.');
    }

    public function edit(Booking $booking): View
    {
        return view('bookings.edit', array_merge(['booking' => $booking], $this->formData()));
    }

    public function update(BookingRequest $request, Booking $booking): RedirectResponse
    {
        $booking->update($request->validated());

        return redirect()->route('bookings.index')->with('status', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()->route('bookings.index')->with('status', 'Booking deleted successfully.');
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
            'cntrOwners' => [
                1 => 'Shipper',
                2 => 'Carrier',
                3 => 'Consignee',
            ],
            'freightTypes' => [
                1 => 'Collect',
                2 => 'Prepaid',
            ],
        ];
    }
}
