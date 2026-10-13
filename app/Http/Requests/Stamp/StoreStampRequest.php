<?php

declare(strict_types=1);

namespace App\Http\Requests\Stamp;

use Illuminate\Foundation\Http\FormRequest;

class StoreStampRequest extends FormRequest
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
            // A stamp without an image is useless, so it is required on create.
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
