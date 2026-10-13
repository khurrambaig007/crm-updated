<?php

declare(strict_types=1);

namespace App\Http\Requests\BlInfo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * BL Info > Stamps screen.
 *
 * The selection is optional: a bill of lading may carry no stamps at all.
 * Each submitted id must reference a real library stamp, so the picker can
 * never persist a stamp that was deleted while the page was open.
 */
class UpdateStampsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'stamp_ids' => ['nullable', 'array'],
            'stamp_ids.*' => ['integer', 'distinct', 'exists:stamps,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'stamp_ids' => 'stamps',
            'stamp_ids.*' => 'stamp',
        ];
    }
}
