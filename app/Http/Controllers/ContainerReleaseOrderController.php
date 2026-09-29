<?php

namespace App\Http\Controllers;

use App\DataTables\ContainerReleaseOrdersDataTable;
use App\Http\Requests\ContainerReleaseOrder\StoreContainerReleaseOrderRequest;
use App\Http\Requests\ContainerReleaseOrder\UpdateContainerReleaseOrderRequest;
use App\Models\Booking;
use App\Models\Commodity;
use App\Models\CompanyProfile;
use App\Models\ContainerReleaseOrder;
use App\Models\Pod;
use App\Models\Pol;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ContainerReleaseOrderController extends Controller
{
    /**
     * Maximum rendered size of the company logo in the exported PDF, in pixels.
     */
    private const PDF_LOGO_MAX_WIDTH = 320;

    private const PDF_LOGO_MAX_HEIGHT = 150;

    public function index(Request $request, ContainerReleaseOrdersDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('container-release-orders.index');
    }

    public function create(Request $request): View
    {
        $prefill = [];

        if ($request->query('booking')) {
            $booking = Booking::findOrFail((int) $request->query('booking'));
            $prefill = [
                'booking_id' => $booking->id,
                'booking_no' => $booking->booking_no,
                'reference_no' => $booking->reference_no,
                'booking_date' => $booking->booking_date?->format('Y-m-d'),
                'cntr_owner' => $booking->cntr_owner,
                'commodity_id' => $booking->commodity,
                'dg_status' => $booking->non_dg,
                'pol_id' => $booking->pol,
                'pofd_id' => $booking->pofd,
            ];
        }

        return view('container-release-orders.create', array_merge([
            'prefill' => $prefill,
        ], $this->formData()));
    }

    public function store(StoreContainerReleaseOrderRequest $request): RedirectResponse
    {
        ContainerReleaseOrder::create($request->validated());

        return redirect()->route('container-release-orders.index')->with('status', 'Container Release Order created successfully.');
    }

    public function edit(ContainerReleaseOrder $containerReleaseOrder): View
    {
        $containerReleaseOrder->load(['commodity', 'pol', 'pofd']);

        return view('container-release-orders.edit', array_merge([
            'cro' => $containerReleaseOrder,
        ], $this->formData()));
    }

    public function update(UpdateContainerReleaseOrderRequest $request, ContainerReleaseOrder $containerReleaseOrder): RedirectResponse
    {
        $containerReleaseOrder->update($request->validated());

        return redirect()->route('container-release-orders.index')->with('status', 'Container Release Order updated successfully.');
    }

    public function destroy(ContainerReleaseOrder $containerReleaseOrder): RedirectResponse
    {
        $containerReleaseOrder->delete();

        return redirect()->route('container-release-orders.index')->with('status', 'Container Release Order deleted successfully.');
    }

    public function exportPdf(ContainerReleaseOrder $containerReleaseOrder): Response
    {
        $containerReleaseOrder->load(['pol', 'pofd', 'commodity']);

        // Branded from the company profile. logoPath() is passed rather than the URL
        // because dompdf only reads local files inside its chroot (base_path()).
        $profile = CompanyProfile::current();

        return Pdf::loadView('container-release-orders.pdf', [
            'cro' => $containerReleaseOrder,
            'brandName' => $profile->displayName(),
            'brandLogoPath' => $profile->logoPath(),
            'brandLogoSize' => $profile->logoDisplaySize(
                self::PDF_LOGO_MAX_WIDTH,
                self::PDF_LOGO_MAX_HEIGHT,
            ),
            'brandContact' => array_filter([
                $profile->website,
                $profile->primaryEmail(),
                $profile->pic_number,
            ]),
        ])
            ->setPaper('a4', 'portrait')
            ->stream("CRO-{$containerReleaseOrder->booking_no}.pdf");
    }

    private function formData(): array
    {
        return [
            'commodities' => Commodity::orderBy('commodity_number')->get(),
            'pols' => Pol::orderBy('city')->get(),
            'pofds' => Pod::orderBy('city')->get(),
            'cntrOwners' => config('dropdowns.container_release_orders.cntr_owner'),
            'dgStatuses' => config('dropdowns.container_release_orders.dg_status'),
        ];
    }
}
