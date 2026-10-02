<?php

namespace App\Http\Requests\CompanyProfile;

class StoreCompanyProfileRequest extends SaveCompanyProfileRequest
{
    /**
     * @return array<int, mixed>
     */
    protected function logoRules(): array
    {
        return ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
    }
}
