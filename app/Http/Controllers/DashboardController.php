<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $profile = CompanyProfile::current();

        return view('dashboard', [
            'user' => $request->user(),
            'showCompanyOnboarding' => ! $profile->exists
                && ! $request->session()->get('company_onboarding_skipped', false)
                && ($request->user()->isSuperAdmin() || $request->user()->can('company_profiles.add')),
        ]);
    }

    public function skipOnboarding(Request $request): RedirectResponse
    {
        $request->session()->put('company_onboarding_skipped', true);

        return redirect()->route('dashboard');
    }
}
