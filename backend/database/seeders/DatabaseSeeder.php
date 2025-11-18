<?php

namespace Database\Seeders;

use App\Models\Task;
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
        // Create 5 users
        $users = User::factory(5)->create();

        // Create tasks for each user
        $users->each(function ($user) {
            Task::factory(5)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
