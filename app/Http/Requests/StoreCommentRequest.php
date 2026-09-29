<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'ticket_id' => [
                'required',
                'integer',
                'exists:tickets,id',
            ],
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'body' => [
                'required',
                'string',
            ],
        ];
    }
}
