<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        if (!DB::table('users')->where('email', $adminEmail)->exists()) {
            DB::table('users')->insert([
                'name'           => 'Administrator',
                'email'          => $adminEmail,
                'password'       => Hash::make($adminPassword),
                'role_id'        => 1,
                'account_status' => 'approved',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        $assistantBase = env('ASSISTANT_BASE_EMAIL', 'assistant');
        $assistantPassword = env('ASSISTANT_PASSWORD', 'secret123');
        $assistantCount = env('ASSISTANT_COUNT', 5);

        for ($i = 1; $i <= $assistantCount; $i++) {

            $email = "{$assistantBase}{$i}@gmail.com";

            if (!DB::table('users')->where('email', $email)->exists()) {
                DB::table('users')->insert([
                    'name'           => "Assistant $i",
                    'email'          => $email,
                    'password'       => Hash::make($assistantPassword),
                    'role_id'        => 3,
                    'account_status' => 'approved',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }
    }
}
