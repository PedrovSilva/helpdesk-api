<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
            ],
            'status' => [
                'required',
                Rule::enum(TicketStatus::class),
            ],
            'priority' => [
                'required',
                Rule::enum(Priority::class),
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'assigned_to' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(
                    'user_role',
                    UserRole::AGENT->value
                ),
            ],
        ];
    }
}
