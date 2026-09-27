<?php

namespace Database\Seeders;

use App\Models\TicketHistories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketHistoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TicketHistories::factory(10)->create();
    }
}
