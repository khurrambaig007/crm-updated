<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentReceiptPayment\StoreAgentReceiptPaymentRequest;
use App\Http\Requests\AgentReceiptPayment\UpdateAgentReceiptPaymentRequest;
use App\Models\Agent;
use App\Models\AgentReceiptPayment;
use App\Models\Currency;
use App\Services\ExchangeRateService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AgentReceiptPaymentController extends Controller
{
    public function index(): RedirectResponse
    {
        $last = AgentReceiptPayment::orderBy('id', 'desc')->first();

        if ($last) {
            return redirect()->route('agent-receipt-payments.edit', $last);
        }

        return redirect()->route('agent-receipt-payments.create');
    }

    public function create(): View
    {
        $arp = new AgentReceiptPayment;
        $total = AgentReceiptPayment::count();
        $current = $total + 1;
        $firstId = AgentReceiptPayment::orderBy('id')->value('id');
        $lastId = AgentReceiptPayment::orderBy('id', 'desc')->value('id');

        return view('agent-receipt-payments.edit', array_merge($this->formData(), [
            'arp' => $arp,
            'total' => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId' => $lastId,
            'prevId' => null,
            'nextId' => null,
        ]));
    }

    public function store(StoreAgentReceiptPaymentRequest $request): RedirectResponse
    {
        $arp = AgentReceiptPayment::create($request->validated());

        return redirect()->route('agent-receipt-payments.edit', $arp)->with('status', 'Agent receipt payment created successfully.');
    }

    public function edit(AgentReceiptPayment $arp): View
    {
        $total = AgentReceiptPayment::count();
        $current = AgentReceiptPayment::where('id', '<=', $arp->id)->count();
        $firstId = AgentReceiptPayment::orderBy('id')->value('id');
        $lastId = AgentReceiptPayment::orderBy('id', 'desc')->value('id');
        $prevId = AgentReceiptPayment::where('id', '<', $arp->id)->orderBy('id', 'desc')->value('id');
        $nextId = AgentReceiptPayment::where('id', '>', $arp->id)->orderBy('id')->value('id');

        return view('agent-receipt-payments.edit', array_merge($this->formData(), [
            'arp' => $arp,
            'total' => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId' => $lastId,
            'prevId' => $prevId,
            'nextId' => $nextId,
        ]));
    }

    public function update(UpdateAgentReceiptPaymentRequest $request, AgentReceiptPayment $arp): RedirectResponse
    {
        $arp->update($request->validated());

        return redirect()->route('agent-receipt-payments.edit', $arp)->with('status', 'Agent receipt payment updated successfully.');
    }

    public function destroy(Request $request, AgentReceiptPayment $arp): RedirectResponse
    {
        try {
            $arp->delete();
        } catch (QueryException $e) {
            return redirect()->route('agent-receipt-payments.index')->with('error', 'This record is in use and cannot be deleted.');
        }

        return redirect()->route('agent-receipt-payments.index')->with('status', 'Agent receipt payment deleted successfully.');
    }

    public function navigate(Request $request, AgentReceiptPayment $arp): JsonResponse
    {
        $total = AgentReceiptPayment::count();
        $current = AgentReceiptPayment::where('id', '<=', $arp->id)->count();
        $firstId = AgentReceiptPayment::orderBy('id')->value('id');
        $lastId = AgentReceiptPayment::orderBy('id', 'desc')->value('id');
        $prevId = AgentReceiptPayment::where('id', '<', $arp->id)->orderBy('id', 'desc')->value('id');
        $nextId = AgentReceiptPayment::where('id', '>', $arp->id)->orderBy('id')->value('id');

        return response()->json([
            'arp' => $arp,
            'total' => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId' => $lastId,
            'prevId' => $prevId,
            'nextId' => $nextId,
        ]);
    }

    public function approve(Request $request, AgentReceiptPayment $arp): RedirectResponse
    {
        $approved = $request->boolean('approved', true);

        $arp->update($approved ? [
            'approved_by' => Auth::user()?->name,
            'approved_on' => now()->toDateTimeString(),
        ] : [
            'approved_by' => null,
            'approved_on' => null,
        ]);

        return redirect()->route('agent-receipt-payments.edit', $arp)->with('status', $approved ? 'Agent receipt payment approved.' : 'Agent receipt payment approval revoked.');
    }

    private function formData(): array
    {
        $currency = Currency::orderBy('exchange_rate_date', 'desc')->first();
        $rates = [];

        if ($currency) {
            $rates = json_decode($currency->exchange_rate ?? '', true) ?: [];
        }

        return [
            'agents' => Agent::orderBy('name')->get(),
            'rates' => $rates,
            'currencyCodes' => ExchangeRateService::CURRENCIES,
        ];
    }
}
