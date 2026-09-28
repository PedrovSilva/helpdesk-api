<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSlaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sla = $this->route('sla');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('slas', 'name')->ignore($sla),
            ],
            'priority' => [
                'required',
                Rule::enum(Priority::class),
                Rule::unique('slas', 'priority')->ignore($sla),
            ],
            'response_time_minutes' => [
                'required',
                'integer',
                'min:1',
            ],
            'resolution_time_minutes' => [
                'required',
                'integer',
                'min:1',
            ],
            'active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
