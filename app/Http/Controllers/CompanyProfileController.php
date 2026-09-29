<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyProfile\StoreCompanyProfileRequest;
use App\Http\Requests\CompanyProfile\UpdateCompanyProfileRequest;
use App\Models\CompanyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyProfileController extends Controller
{
    private const LOGO_DIRECTORY = 'company-profile';

    public function edit(): View
    {
        $profile = CompanyProfile::current();

        return view('company-profiles.edit', [
            'profile' => $profile,
            'isNew' => ! $profile->exists,
        ]);
    }

    public function store(StoreCompanyProfileRequest $request): RedirectResponse
    {
        // The profile is a singleton, so a second concurrent submission must never
        // create a duplicate row.
        if (CompanyProfile::hasProfile()) {
            return redirect()->route('company-profile.index')
                ->with('error', 'A company profile already exists.');
        }

        $profile = new CompanyProfile;
        $this->fillProfile($profile, $request);
        $profile->save();

        return redirect()->route('company-profile.index')
            ->with('status', 'Company profile created successfully.');
    }

    public function update(UpdateCompanyProfileRequest $request): RedirectResponse
    {
        $profile = CompanyProfile::current();

        if (! $profile->exists) {
            return redirect()->route('company-profile.index')
                ->with('error', 'No company profile has been created yet.');
        }

        $this->fillProfile($profile, $request);
        $profile->save();

        return redirect()->route('company-profile.index')
            ->with('status', 'Company profile updated successfully.');
    }

    /**
     * Apply the validated scalar and repeatable fields to the profile.
     */
    private function fillProfile(CompanyProfile $profile, Request $request): void
    {
        $profile->fill($request->safe()->only([
            'name',
            'subtitle',
            'website',
            'number',
            'pic_name',
            'pic_email',
            'pic_number',
            'message',
        ]));

        $profile->emails = CompanyProfile::normalizeEmails($request->input('emails', []));
        $profile->custom_fields = CompanyProfile::normalizeCustomFields($request->input('custom_fields', []));

        $this->syncLogo($request, $profile);
    }

    /**
     * Store, replace or clear the company logo.
     *
     * Only reachable after the form request has already validated the upload as an
     * image within the allowed mime list and size limit.
     */
    private function syncLogo(Request $request, CompanyProfile $profile): void
    {
        if ($request->hasFile('logo')) {
            $this->deleteLogoFile($profile->logo);

            $profile->logo = $request->file('logo')->store(self::LOGO_DIRECTORY, 'public');

            return;
        }

        if ($request->boolean('remove_logo')) {
            $this->deleteLogoFile($profile->logo);

            $profile->logo = null;
        }
    }

    private function deleteLogoFile(?string $path): void
    {
        if (filled($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
