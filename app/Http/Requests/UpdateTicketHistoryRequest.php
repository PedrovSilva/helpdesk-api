<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'event' => [
                'required',
                'string',
                'max:255',
            ],
            'from_value' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'to_value' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'metadata' => [
                'sometimes',
                'nullable',
                'array',
            ],
        ];
    }
}
