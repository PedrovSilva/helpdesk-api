<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'test employee',
            'email' => 'test.employee@example.com',
            'password'=> bcrypt('123456'),
            'user_role' => UserRole::AGENT,
        ]);

         User::factory()->create([
            'name' => 'test customer',
            'email' => 'test.customer@example.com',
            'password'=> bcrypt('123456'),
            'user_role' => UserRole::COSTUMER,
        ]);

        User::factory()->create([
            'name' => 'test admin',
            'email' => 'test.admin@example.com',
            'password'=> bcrypt('123456'),
            'user_role' => UserRole::ADMIN,
        ]);

        $this->call([
            CategoriesSeeder::class,
            TicketsSeeder::class,
            AttachmentsSeeder::class,
            CommentsSeeder::class,
            SlasSeeder::class,
            TicketHistoriesSeeder::class,
        ]);
    }
}
