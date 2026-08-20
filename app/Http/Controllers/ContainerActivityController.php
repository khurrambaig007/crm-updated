<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContainerActivity\StoreContainerActivityRequest;
use App\Http\Requests\ContainerActivity\UpdateContainerActivityRequest;
use App\Models\Agent;
use App\Models\Carrier;
use App\Models\ContainerActivity;
use App\Models\ContainerActivityDetail;
use App\Models\Pol;
use App\Models\VesselVoyage;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContainerActivityController extends Controller
{
    public function index(): RedirectResponse
    {
        $last = ContainerActivity::orderBy('id', 'desc')->first();

        if ($last) {
            return redirect()->route('container-activities.edit', $last);
        }

        return redirect()->route('container-activities.create');
    }

    public function create(): View
    {
        $record = new ContainerActivity;
        $total = ContainerActivity::count();
        $current = $total + 1;
        $firstId = ContainerActivity::orderBy('id')->value('id');
        $lastId = ContainerActivity::orderBy('id', 'desc')->value('id');

        return view('container-activities.edit', array_merge($this->formData(), [
            'record' => $record,
            'total' => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId' => $lastId,
            'prevId' => null,
            'nextId' => null,
        ]));
    }

    public function store(StoreContainerActivityRequest $request): RedirectResponse
    {
        $record = ContainerActivity::create($request->validated());

        return redirect()->route('container-activities.edit', $record)
            ->with('status', 'Container Activity created successfully.');
    }

    public function edit(ContainerActivity $containerActivity): View
    {
        $containerActivity->load(['vesselVoyage', 'details']);
        $total = ContainerActivity::count();
        $current = ContainerActivity::where('id', '<=', $containerActivity->id)->count();
        $firstId = ContainerActivity::orderBy('id')->value('id');
        $lastId = ContainerActivity::orderBy('id', 'desc')->value('id');
        $prevId = ContainerActivity::where('id', '<', $containerActivity->id)->orderBy('id', 'desc')->value('id');
        $nextId = ContainerActivity::where('id', '>', $containerActivity->id)->orderBy('id')->value('id');

        return view('container-activities.edit', array_merge($this->formData(), [
            'record' => $containerActivity,
            'total' => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId' => $lastId,
            'prevId' => $prevId,
            'nextId' => $nextId,
        ]));
    }

    public function update(UpdateContainerActivityRequest $request, ContainerActivity $containerActivity): RedirectResponse
    {
        $containerActivity->update($request->validated());

        return redirect()->route('container-activities.edit', $containerActivity)
            ->with('status', 'Container Activity updated successfully.');
    }

    public function destroy(Request $request, ContainerActivity $containerActivity): RedirectResponse
    {
        try {
            $containerActivity->details()->delete();
            $containerActivity->delete();
        } catch (QueryException $e) {
            return redirect()->route('container-activities.index')
                ->with('error', 'This Container Activity is in use and cannot be deleted.');
        }

        return redirect()->route('container-activities.index')
            ->with('status', 'Container Activity deleted successfully.');
    }

    public function navigate(Request $request, ContainerActivity $containerActivity): JsonResponse
    {
        $containerActivity->load(['vesselVoyage', 'details']);
        $total = ContainerActivity::count();
        $current = ContainerActivity::where('id', '<=', $containerActivity->id)->count();
        $firstId = ContainerActivity::orderBy('id')->value('id');
        $lastId = ContainerActivity::orderBy('id', 'desc')->value('id');
        $prevId = ContainerActivity::where('id', '<', $containerActivity->id)->orderBy('id', 'desc')->value('id');
        $nextId = ContainerActivity::where('id', '>', $containerActivity->id)->orderBy('id')->value('id');

        return response()->json([
            'activity' => $containerActivity,
            'details' => $containerActivity->details,
            'total' => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId' => $lastId,
            'prevId' => $prevId,
            'nextId' => $nextId,
        ]);
    }

    public function storeDetail(Request $request, ContainerActivity $containerActivity): JsonResponse
    {
        $validated = $request->validate($this->detailRules());

        $detail = $containerActivity->details()->create($validated);

        return response()->json(['detail' => $detail, 'message' => 'Detail saved.']);
    }

    public function updateDetail(Request $request, ContainerActivity $containerActivity, ContainerActivityDetail $detail): JsonResponse
    {
        $validated = $request->validate($this->detailRules());

        $detail->update($validated);

        return response()->json(['detail' => $detail, 'message' => 'Detail updated.']);
    }

    public function destroyDetail(Request $request, ContainerActivity $containerActivity, ContainerActivityDetail $detail): JsonResponse
    {
        $detail->delete();

        return response()->json(['message' => 'Detail deleted.']);
    }

    private function detailRules(): array
    {
        return [
            'containe_no' => ['nullable', 'string', 'max:191'],
            'size_type' => ['nullable', 'string', 'max:191'],
            'principle' => ['nullable', 'string', 'max:191'],
            'bl_number' => ['nullable', 'string', 'max:191'],
            'booking_number' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', 'string', 'max:191'],
            'cargo_type' => ['nullable', 'string', 'max:191'],
            'one_door_open' => ['nullable', 'string', 'max:191'],
            'last_activity' => ['nullable', 'string', 'max:191'],
            'system_remarks' => ['nullable', 'string', 'max:191'],
            'vessel_ts1' => ['nullable', 'string', 'max:191'],
            'voyage_ts1' => ['nullable', 'string', 'max:191'],
            'sailing_date_ts1' => ['nullable', 'string', 'max:191'],
            'vessel_ts2' => ['nullable', 'string', 'max:191'],
            'voyage_ts2' => ['nullable', 'string', 'max:191'],
            'sailing_date_ts2' => ['nullable', 'string', 'max:191'],
            'vessel_ts3' => ['nullable', 'string', 'max:191'],
            'voyage_ts3' => ['nullable', 'string', 'max:191'],
            'sailing_date_ts3' => ['nullable', 'string', 'max:191'],
        ];
    }

    private function formData(): array
    {
        $vessels = VesselVoyage::orderBy('vessel_name')->get();

        return [
            'agents' => Agent::orderBy('code')->get(),
            'carriers' => Carrier::orderBy('name')->get(),
            'vessels' => $vessels,
            'pols' => Pol::orderBy('city')->get(),
            'activityTypes' => config('dropdowns.container_activities_activity', []),
            'cargoTypes' => config('dropdowns.container_activities_cargo_type', []),
            'statuses' => config('dropdowns.container_activities_status', []),
            // Flat list of vessel names for the detail grid dropdown.
            'vesselNames' => $vessels->pluck('vessel_name')->all(),
            // Lookup so the grid can auto-fill Voyage (TS1/2/3) from the picked Vessel.
            'vesselVoyageLookup' => $vessels->pluck('voyage_number', 'vessel_name')->all(),
        ];
    }
}
