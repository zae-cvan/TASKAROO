<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $existing = User::where('email', $email)->first();
        if ($existing) {
            $existing->role = 'admin';
            $existing->is_active = true;
            $existing->save();
            return;
        }

        User::create([
            'name' => env('ADMIN_NAME', 'Administrator'),
            'email' => $email,
            'password' => Hash::make(env('ADMIN_PASSWORD', 'AdminPass#123')),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
