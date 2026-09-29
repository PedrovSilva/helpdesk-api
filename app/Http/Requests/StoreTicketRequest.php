<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
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
            'priority' => [
                'sometimes',
                Rule::enum(Priority::class),
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'customer_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(
                    'user_role',
                    UserRole::CUSTOMER->value
                ),
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
