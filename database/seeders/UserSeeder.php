<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default credentials
        $check = \App\Models\User::where('name', 'Superuser')
            ->first();

        if (!$check) {
            \App\Models\User::insert([
                'name'  => 'Superuser',
                'username' => 'superuser',
                'email' => 'dewa17a@gmail.com',
                'email_verified_at',
                'password' => Hash::make('12345678'),
                'password_masked' => '12345678',
                'status' => true,
                'branch_id' => 1,
                'role_id' => 1,
                'karyawan_id' => null,
                'image' => null,
                'remember_token' => null,
            ]);
        }
    }
}
