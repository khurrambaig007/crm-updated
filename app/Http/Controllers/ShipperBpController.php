<?php

namespace App\Http\Controllers;

use App\DataTables\ShipperBpsDataTable;
use App\Http\Requests\ShipperBp\StoreShipperBpRequest;
use App\Http\Requests\ShipperBp\UpdateShipperBpRequest;
use App\Models\Agent;
use App\Models\Pol;
use App\Models\ShipperBp;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipperBpController extends Controller
{
    public function index(Request $request, ShipperBpsDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('shipper-bps.index');
    }

    public function create(): View
    {
        return view('shipper-bps.create', [
            'nextCode' => $this->nextShipperCode(),
            'agents' => Agent::orderBy('code')->get(),
            'pols' => Pol::orderBy('city')->get(),
        ]);
    }

    public function store(StoreShipperBpRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = $this->nextShipperCode();
        $data['shipper'] = $request->boolean('shipper');
        $data['ca'] = $request->boolean('ca');
        $data['consignee'] = $request->boolean('consignee');

        ShipperBp::create($data);

        return redirect()->route('shipper-bps.index')->with('status', 'Shipper/BP created successfully.');
    }

    public function edit(ShipperBp $shipperBp): View
    {
        return view('shipper-bps.edit', [
            'shipperBp' => $shipperBp,
            'agents' => Agent::orderBy('code')->get(),
            'pols' => Pol::orderBy('city')->get(),
        ]);
    }

    public function update(UpdateShipperBpRequest $request, ShipperBp $shipperBp): RedirectResponse
    {
        $data = $request->validated();
        $data['shipper'] = $request->boolean('shipper');
        $data['ca'] = $request->boolean('ca');
        $data['consignee'] = $request->boolean('consignee');

        $shipperBp->update($data);

        return redirect()->route('shipper-bps.index')->with('status', 'Shipper/BP updated successfully.');
    }

    public function destroy(Request $request, ShipperBp $shipperBp): RedirectResponse
    {
        try {
            $shipperBp->delete();
        } catch (QueryException $e) {
            return redirect()->route('shipper-bps.index')->with('error', 'This shipper/BP is in use and cannot be deleted.');
        }

        return redirect()->route('shipper-bps.index')->with('status', 'Shipper/BP deleted successfully.');
    }

    private function nextShipperCode(): string
    {
        $last = ShipperBp::query()->orderByDesc('id')->value('code');
        $lastNumber = $last ? (int) substr($last, 5) : 0;

        return 'SHBP-'.str_pad((string) ($lastNumber + 1), 10, '0', STR_PAD_LEFT);
    }
}
