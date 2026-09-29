<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttachmentRequest extends FormRequest
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
            'file_path' => [
                'required',
                'string',
                'max:255',
            ],
            'file_name' => [
                'required',
                'string',
                'max:255',
            ],
            'mime_type' => [
                'required',
                'string',
                'max:255',
            ],
            'file_size' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}
