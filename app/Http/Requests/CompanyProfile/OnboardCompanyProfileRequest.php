<?php

namespace App\Http\Requests\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;

class OnboardCompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'company_email' => ['required', 'email', 'max:191'],
            'website' => ['nullable', 'string', 'max:191'],
            'number' => ['required', 'string', 'max:191'],
            'subtitle' => ['nullable', 'string', 'max:191'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'pic_name' => ['nullable', 'string', 'max:191'],
            'pic_email' => ['nullable', 'email', 'max:191'],
            'pic_number' => ['nullable', 'string', 'max:191'],
            'message' => ['nullable', 'string'],
        ];
    }
}
