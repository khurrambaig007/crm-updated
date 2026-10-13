<?php

declare(strict_types=1);

namespace App\Http\Requests\Stamp;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStampRequest extends FormRequest
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
            // Optional on edit: submitting no file keeps the stored image, which
            // is what lets someone rename a stamp without re-uploading it.
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }
}
