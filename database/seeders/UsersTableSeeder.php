<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Can update all
        User::create([
            'fullname' => 'System Admin',
            'phone_number' => '09123456789',
            'gender' => 'female',
            'email' => 'admin@ulc.com',
            'password' => Hash::make('123456789'),
            'role' => 'admin',
            'status' => 'verified',
            'created_by' => 'Admin'
        ]);

        //Manila Area
        User::create([
            'fullname' => 'Erica Tinio',
            'phone_number' => '09123456789',
            'gender' => 'female',
            'email' => 'ericatinio@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'secretary',
            'status' => 'verified',
            'created_by' => 'Admin'
        ]);

        //Valenzuala Area
        User::create([
            'fullname' => 'Princess Divine Gambet',
            'phone_number' => '09123456789',
            'gender' => 'female',
            'email' => 'princessdivine@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'secretary',
            'status' => 'verified',
            'created_by' => 'Admin'

        ]);

        //Caloocan Area
        User::create([
            'fullname' => 'Faljemarick Reynante',
            'phone_number' => '09123456789',
            'gender' => 'female',
            'email' => 'faljemarickreynante@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'secretary',
            'status' => 'verified',
            'created_by' => 'Admin'

        ]);

        //Financial Counselor
        User::create([
            'fullname' => 'Hydie Cadiz',
            'phone_number' => '09123456789',
            'gender' => 'female',
            'email' => 'hydiecadiz@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'secretary',
            'status' => 'verified',
            'created_by' => 'Admin'

        ]);
    }
}
