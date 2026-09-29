<?php

namespace App\Http\Requests\CompanyProfile;

class StoreCompanyProfileRequest extends SaveCompanyProfileRequest
{
    /**
     * A brand new profile has nothing to fall back on, so the logo is mandatory.
     *
     * @return array<int, mixed>
     */
    protected function logoRules(): array
    {
        return ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
    }
}
