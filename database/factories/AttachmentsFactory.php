<?php

namespace Database\Factories;

use App\Models\Attachments;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tickets;
/**
 * @extends Factory<Attachments>
 */
class AttachmentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Tickets::factory(),
            'file_path' => fake()->word() . '.pdf',
            'file_name' => fake()->word() . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->randomNumber(6),
        ];
    }
}
