<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketHistorySeeder extends Seeder
{
    public function run(): void
    {
        $tickets = Ticket::query()->get();
        $users = User::query()->get();

        TicketHistory::factory()
            ->count(30)
            ->recycle($tickets)
            ->recycle($users)
            ->create();
    }
}
