<?php

namespace App\Http\Controllers;

use App\DataTables\AgentsDataTable;
use App\Http\Requests\Agent\StoreAgentRequest;
use App\Http\Requests\Agent\UpdateAgentRequest;
use App\Models\Agent;
use App\Models\ContainerSize;
use App\Models\Pol;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgentController extends Controller
{
    public function index(Request $request, AgentsDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('agents.index');
    }

    public function create(): View
    {
        return view('agents.create', [
            'pols' => Pol::orderBy('city')->get(),
            'containerSizes' => ContainerSize::orderBy('size')->get(),
        ]);
    }

    public function store(StoreAgentRequest $request): RedirectResponse
    {
        $agent = Agent::create($request->validated());

        $agent->containerSizes()->sync($request->filled('container_size_id') ? [$request->integer('container_size_id')] : []);

        return redirect()->route('agents.index')->with('status', 'Agent created successfully.');
    }

    public function edit(Agent $agent): View
    {
        return view('agents.edit', [
            'agent' => $agent,
            'pols' => Pol::orderBy('city')->get(),
            'containerSizes' => ContainerSize::orderBy('size')->get(),
        ]);
    }

    public function update(UpdateAgentRequest $request, Agent $agent): RedirectResponse
    {
        $agent->update($request->validated());

        $agent->containerSizes()->sync($request->filled('container_size_id') ? [$request->integer('container_size_id')] : []);

        return redirect()->route('agents.index')->with('status', 'Agent updated successfully.');
    }

    public function destroy(Request $request, Agent $agent): RedirectResponse
    {
        try {
            $agent->delete();
        } catch (QueryException $e) {
            return redirect()->route('agents.index')->with('error', 'This agent is in use and cannot be deleted.');
        }

        return redirect()->route('agents.index')->with('status', 'Agent deleted successfully.');
    }
}
