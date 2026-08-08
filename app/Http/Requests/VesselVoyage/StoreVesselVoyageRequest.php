<?php

namespace App\Http\Requests\VesselVoyage;

use Illuminate\Foundation\Http\FormRequest;

class StoreVesselVoyageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vessel_name' => ['required', 'string', 'max:255'],
            'voyage_number' => ['required', 'string', 'max:255'],
        ];
    }
}
