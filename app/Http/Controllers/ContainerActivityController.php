<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContainerActivity\StoreContainerActivityRequest;
use App\Http\Requests\ContainerActivity\UpdateContainerActivityRequest;
use App\Models\Agent;
use App\Models\Carrier;
use App\Models\ContainerActivity;
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
            'record'  => $record,
            'total'   => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId'  => $lastId,
            'prevId'  => null,
            'nextId'  => null,
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
        $containerActivity->load('vesselVoyage');
        $total = ContainerActivity::count();
        $current = ContainerActivity::where('id', '<=', $containerActivity->id)->count();
        $firstId = ContainerActivity::orderBy('id')->value('id');
        $lastId = ContainerActivity::orderBy('id', 'desc')->value('id');
        $prevId = ContainerActivity::where('id', '<', $containerActivity->id)->orderBy('id', 'desc')->value('id');
        $nextId = ContainerActivity::where('id', '>', $containerActivity->id)->orderBy('id')->value('id');

        return view('container-activities.edit', array_merge($this->formData(), [
            'record'  => $containerActivity,
            'total'   => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId'  => $lastId,
            'prevId'  => $prevId,
            'nextId'  => $nextId,
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
        $containerActivity->load('vesselVoyage');
        $total = ContainerActivity::count();
        $current = ContainerActivity::where('id', '<=', $containerActivity->id)->count();
        $firstId = ContainerActivity::orderBy('id')->value('id');
        $lastId = ContainerActivity::orderBy('id', 'desc')->value('id');
        $prevId = ContainerActivity::where('id', '<', $containerActivity->id)->orderBy('id', 'desc')->value('id');
        $nextId = ContainerActivity::where('id', '>', $containerActivity->id)->orderBy('id')->value('id');

        return response()->json([
            'activity' => $containerActivity,
            'total'    => $total,
            'current'  => $current,
            'firstId'  => $firstId,
            'lastId'   => $lastId,
            'prevId'   => $prevId,
            'nextId'   => $nextId,
        ]);
    }

    private function formData(): array
    {
        return [
            'agents'        => Agent::orderBy('code')->get(),
            'carriers'      => Carrier::orderBy('name')->get(),
            'vessels'       => VesselVoyage::orderBy('vessel_name')->get(),
            'pols'          => Pol::orderBy('city')->get(),
            'activityTypes' => config('dropdowns.container_activities.activity', []),
        ];
    }
}
