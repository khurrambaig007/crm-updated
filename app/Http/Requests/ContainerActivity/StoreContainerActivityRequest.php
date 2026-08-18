<?php

namespace App\Http\Requests\ContainerActivity;

use Illuminate\Foundation\Http\FormRequest;

class StoreContainerActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doc_no'                 => ['nullable', 'string', 'max:191'],
            'activity_date'          => ['nullable', 'date'],
            'agent'                  => ['nullable', 'string', 'max:191'],
            'activity'               => ['nullable', 'string', 'max:191'],
            'free_days'              => ['nullable', 'integer'],
            'bl_number'              => ['nullable', 'string', 'max:191'],
            'booking_number'         => ['nullable', 'string', 'max:191'],
            'final_destination_code' => ['nullable', 'string', 'max:191'],
            'pod_code'               => ['nullable', 'string', 'max:191'],
            'destination_agent'      => ['nullable', 'string', 'max:191'],
            'thru_bl'                => ['nullable', 'boolean'],
            'sailing_date'           => ['nullable', 'date'],
            'vessel_voyage_id'       => ['nullable', 'exists:vessel_voyages,id'],
            'voyage_number'          => ['nullable', 'string', 'max:191'],
            'location'               => ['nullable', 'string', 'max:191'],
            'carrier'                => ['nullable', 'string', 'max:191'],
            'ts_1_port'              => ['nullable', 'string', 'max:191'],
            'ts_1_agent'             => ['nullable', 'string', 'max:191'],
            'ts_2_port'              => ['nullable', 'string', 'max:191'],
            'ts_2_agent'             => ['nullable', 'string', 'max:191'],
            'ts_3_port'              => ['nullable', 'string', 'max:191'],
            'ts_3_agent'             => ['nullable', 'string', 'max:191'],
            'remarks'                => ['nullable', 'string'],
        ];
    }
}
