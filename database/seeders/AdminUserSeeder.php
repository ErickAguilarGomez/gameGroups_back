<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name'           => 'Administrator',
                'email'          => $adminEmail,
                'password'       => bcrypt($adminPassword),
                'role_id'        => 1,
                'account_status' => 'approved',
            ]);
        }

        $assistantBase = env('ASSISTANT_BASE_EMAIL', 'assistant');
        $assistantPassword = env('ASSISTANT_PASSWORD', 'secret123');
        $assistantCount = env('ASSISTANT_COUNT', 5);

        for ($i = 1; $i <= $assistantCount; $i++) {

            $email = "{$assistantBase}{$i}@gmail.com";

            if (!User::where('email', $email)->exists()) {
                User::create([
                    'name'           => "Assistant $i",
                    'email'          => $email,
                    'password'       => bcrypt($assistantPassword),
                    'role_id'        => 3,
                    'account_status' => 'approved',
                ]);
            }
        }
    }
}

