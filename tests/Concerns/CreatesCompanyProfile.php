<?php

namespace Tests\Concerns;

use App\Models\CompanyProfile;

trait CreatesCompanyProfile
{
    /**
     * Seed a company profile that satisfies EnsureCompanyProfileIsComplete, so tests
     * that render an app-layout screen are not bounced to the profile editor.
     *
     * The logo is written as a plain path because completeness only checks that a
     * path is present; no file needs to exist on disk.
     */
    protected function seedCompleteCompanyProfile(array $attributes = []): CompanyProfile
    {
        return CompanyProfile::create(array_merge([
            'name' => 'Acme Logistics',
            'logo' => 'company-profile/test-logo.png',
            'emails' => ['info@acme.test'],
        ], $attributes));
    }
}
