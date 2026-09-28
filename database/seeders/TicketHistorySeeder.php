<?php

namespace Database\Seeders;

use App\Models\TicketHistory;
use Illuminate\Database\Seeder;

class TicketHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TicketHistory::factory(10)->create();
    }
}
