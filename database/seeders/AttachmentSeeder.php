<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $tickets = Ticket::query()->get();

        Attachment::factory()
            ->count(20)
            ->recycle($tickets)
            ->create();
    }
}
