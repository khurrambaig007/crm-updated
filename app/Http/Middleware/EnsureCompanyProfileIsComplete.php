<?php

namespace App\Http\Middleware;

use App\Models\CompanyProfile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyProfileIsComplete
{
    /**
     * Routes that stay reachable while the company profile is incomplete.
     *
     * The profile routes obviously must be, and logout must be so a user is never
     * trapped. The personal profile and theme routes are account chrome that
     * expose no business data, so the topbar avatar and theme switcher keep
     * working while the rest of the application is locked.
     */
    private const EXEMPT = [
        'company-profile.*',
        'logout',
        'profile.edit',
        'profile.password',
        'theme.update',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs(...self::EXEMPT)) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Never lock out a user who is not allowed to open the screen that would
        // let them fix the problem. Prefer a missing gate over a hard lockout.
        if (! $user->isSuperAdmin() && ! $user->can('company_profiles.view')) {
            return $next($request);
        }

        if (CompanyProfile::missingFields() === []) {
            return $next($request);
        }

        return redirect()->route('company-profile.index')
            ->with('error', 'Complete your company profile to continue using the system.');
    }
}
