<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $tickets = Ticket::query()->get();
        $users = User::query()->get();

        Comment::factory()
            ->count(30)
            ->recycle($tickets)
            ->recycle($users)
            ->create();
    }
}
