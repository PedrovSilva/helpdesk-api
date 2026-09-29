<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::query()->where('user_role', UserRole::CUSTOMER)->get();
        $agents = User::query()->where('user_role', UserRole::AGENT)->get();
        $categories = Category::query()->get();

        Ticket::factory()
            ->count(10)
            ->recycle($customers)
            ->recycle($categories)
            ->create();

        Ticket::factory()
            ->count(5)
            ->inProgress()
            ->recycle($customers)
            ->recycle($categories)
            ->sequence(fn () => [
                'assigned_to' => $agents->random()->id,
            ])
            ->create();

        Ticket::factory()
            ->count(3)
            ->high()
            ->recycle($customers)
            ->recycle($categories)
            ->create();

        Ticket::factory()
            ->count(2)
            ->resolved()
            ->recycle($customers)
            ->recycle($categories)
            ->create();

        Ticket::factory()
            ->count(2)
            ->closed()
            ->recycle($customers)
            ->recycle($categories)
            ->create();
    }
}
