<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $command = $this->command;

        User::factory()->count(3)->create()->each(function ($user) use ($command) {
            $plainToken = Str::random(60);
            $user->api_token = hash('sha256', $plainToken);
            $user->save();

            $command->info("User {$user->name}, token: {$plainToken}");
        });
    }
}
