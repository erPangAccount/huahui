<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'admin',
                'email' => 'example@example.com',
                'email_verified_at' => \Carbon\Carbon::now(),
                'password' => \Illuminate\Support\Facades\Hash::make('secret')
            ]
        ];

        foreach ($users as $user) {
            \App\Models\User::query()->firstOrCreate($user);
        }
    }
}
