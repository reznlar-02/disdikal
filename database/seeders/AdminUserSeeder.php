<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Satu-satunya akun yang bisa login ke aplikasi ini. Idempoten lewat
     * firstOrCreate supaya aman dijalankan ulang tiap deploy tanpa menimpa
     * password yang mungkin sudah diganti admin sendiri.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => env('ADMIN_DEFAULT_PASSWORD', 'ganti-password-ini'),
            ]
        );
    }
}
