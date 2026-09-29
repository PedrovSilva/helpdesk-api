<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        $extension = fake()->randomElement(['pdf', 'png', 'jpg', 'txt']);
        $fileName = fake()->unique()->word().'.'.$extension;

        return [
            'ticket_id' => Ticket::factory(),
            'file_path' => 'attachments/'.Str::uuid().'.'.$extension,
            'file_name' => $fileName,
            'mime_type' => match ($extension) {
                'pdf' => 'application/pdf',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'txt' => 'text/plain',
            },
            'file_size' => fake()->numberBetween(1024, 5_000_000),
        ];
    }
}
