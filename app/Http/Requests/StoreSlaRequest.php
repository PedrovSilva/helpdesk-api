<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSlaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:slas,name',
            ],
            'priority' => [
                'required',
                Rule::enum(Priority::class),
                'unique:slas,priority',
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
