<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Carrier;
use App\Models\Charge;
use App\Models\Commodity;
use App\Models\ContainerKind;
use App\Models\ContainerSize;
use App\Models\ContainerType;
use App\Models\Investor;
use App\Models\Party;
use App\Models\Pol;
use App\Models\SettlementType;
use App\Models\ShipperBp;
use App\Models\SlotTerm;
use App\Models\SubCompany;
use App\Models\Supplier;
use App\Models\VesselVoyage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SystemOperationsController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('system-operations.index', [
            'user' => $request->user(),
            'stats' => [
                'container_sizes' => ContainerSize::count(),
                'port_locations' => Pol::count(),
                'carriers' => Carrier::count(),
                'agents' => Agent::count(),
                'container_types' => ContainerType::count(),
                'container_kinds' => ContainerKind::count(),
                'commodities' => Commodity::count(),
                'vessel_voyages' => VesselVoyage::count(),
                'charges' => Charge::count(),
                'slots' => SlotTerm::count(),
                'investors' => Investor::count(),
                'parties' => Party::count(),
                'settlement_types' => SettlementType::count(),
                'shipper_bps' => ShipperBp::count(),
                'suppliers' => Supplier::count(),
                'sub_companies' => SubCompany::count(),
            ],
        ]);
    }
}
