<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        DB::table('users')->insert([
            [
                'name' => 'System Admin',
                'email' => 'system@mail.com',
                'phone' => '01710001337',
                'username' => 'SystemAdmin',
                'password' => Hash::make('123456'),
                'type' => 'ADMIN',
                'gender' => 'MALE',
                'address' => 'Dhaka',
                'blood_group' => 'B+',
                'marital_status' => 'SINGLE',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Patient User 1',
                'email' => 'patient1@health.com',
                'phone' => '01914441334',
                'username' => 'Patient1',
                'password' => Hash::make('123456'),
                'type' => 'PATIENT',
                'gender' => 'MALE',
                'address' => 'Chittagong',
                'blood_group' => 'A+',
                'marital_status' => 'MARRIED',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
