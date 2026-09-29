<?php

namespace Database\Seeders;

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
        User::factory()
            ->admin()
            ->create([
                'name' => 'System Administrator',
                'email' => 'admin@helpdesk.test',
            ]);

        User::factory()
            ->agent()
            ->create([
                'name' => 'Support Agent',
                'email' => 'agent@helpdesk.test',
            ]);

        User::factory()
            ->customer()
            ->create([
                'name' => 'Customer',
                'email' => 'customer@helpdesk.test',
            ]);

        $this->call([
            CategorySeeder::class,
            SlaSeeder::class,
            TicketSeeder::class,
            CommentSeeder::class,
            AttachmentSeeder::class,
        ]);
    }
}
