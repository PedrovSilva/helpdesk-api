<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTicketRequest extends FormRequest
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
            'status' => [
                'sometimes',
                Rule::enum(TicketStatus::class),
            ],
            'priority' => [
                'sometimes',
                Rule::enum(Priority::class),
            ],
            'category_id' => [
                'sometimes',
                'integer',
                'exists:categories,id',
            ],
            'customer_id' => [
                'sometimes',
                'integer',
                Rule::exists('users', 'id')->where(
                    'user_role',
                    UserRole::CUSTOMER->value
                ),
            ],
            'assigned_to' => [
                'sometimes',
                'integer',
                Rule::exists('users', 'id')->where(
                    'user_role',
                    UserRole::AGENT->value
                ),
            ],
            'sla_id' => [
                'sometimes',
                'integer',
                'exists:slas,id',
            ],
        ];
    }
}
