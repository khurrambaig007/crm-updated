<?php

namespace App\Http\Controllers;

use App\DataTables\CostsDataTable;
use App\Http\Requests\Cost\StoreCostRequest;
use App\Http\Requests\Cost\UpdateCostRequest;
use App\Models\Agent;
use App\Models\Carrier;
use App\Models\ContainerSize;
use App\Models\ContainerType;
use App\Models\Cost;
use App\Models\Label;
use App\Models\LabelCollection;
use App\Models\Pol;
use App\Models\SlotTerm;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CostController extends Controller
{
    public function index(Request $request, CostsDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('costs.index');
    }

    public function create(): View
    {
        return view('costs.create', $this->lookupData());
    }

    public function store(StoreCostRequest $request): RedirectResponse
    {
        $cost = Cost::create($request->safe()->except(['labels', 'label_collections']));

        $this->syncLabels($cost, $request->input('labels', []), Label::class);
        $this->syncLabels($cost, $request->input('label_collections', []), LabelCollection::class);

        return redirect()->route('costs.index')->with('status', 'Cost created successfully.');
    }

    public function edit(Cost $cost): View
    {
        $cost->load(['labels', 'labelCollections']);

        return view('costs.edit', array_merge([
            'cost' => $cost,
        ], $this->lookupData()));
    }

    public function update(UpdateCostRequest $request, Cost $cost): RedirectResponse
    {
        $cost->update($request->safe()->except(['labels', 'label_collections']));

        $this->syncLabels($cost, $request->input('labels', []), Label::class);
        $this->syncLabels($cost, $request->input('label_collections', []), LabelCollection::class);

        return redirect()->route('costs.index')->with('status', 'Cost updated successfully.');
    }

    public function destroy(Request $request, Cost $cost): RedirectResponse
    {
        try {
            $cost->delete();
        } catch (QueryException $e) {
            return redirect()->route('costs.index')->with('error', 'This cost is in use and cannot be deleted.');
        }

        return redirect()->route('costs.index')->with('status', 'Cost deleted successfully.');
    }

    private function syncLabels(Cost $cost, array $items, string $model): void
    {
        $model::where('cost_id', $cost->id)->delete();

        foreach ($items as $item) {
            if (blank($item['key'] ?? null) && blank($item['value'] ?? null)) {
                continue;
            }

            $model::create([
                'cost_id' => $cost->id,
                'user_id' => auth()->id(),
                'key' => $item['key'] ?? null,
                'value' => $item['value'] ?? null,
            ]);
        }
    }

    private function lookupData(): array
    {
        return [
            'pols' => Pol::orderBy('city')->get(),
            'pods' => Pol::orderBy('city')->get(),
            'containerTypes' => ContainerType::orderBy('name')->get(),
            'feeders' => Carrier::orderBy('name')->get(),
            'containerSizes' => ContainerSize::orderBy('size')->get(),
            'slotTerms' => SlotTerm::orderBy('term')->get(),
            'agents' => Agent::orderBy('name')->get(),
        ];
    }
}
