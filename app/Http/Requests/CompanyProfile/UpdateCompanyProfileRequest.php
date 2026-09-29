<?php

namespace App\Http\Requests\CompanyProfile;

use App\Models\CompanyProfile;

class UpdateCompanyProfileRequest extends SaveCompanyProfileRequest
{
    /**
     * Only demanded again while nothing is stored, so an existing logo survives a
     * re-save that does not upload a replacement.
     *
     * @return array<int, mixed>
     */
    protected function logoRules(): array
    {
        return [
            CompanyProfile::current()->logo ? 'nullable' : 'required',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
        ];
    }
}
