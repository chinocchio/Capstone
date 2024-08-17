<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Chino Noble ',
                'section' => '2H',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Edward Sarte ',
                'section' => '2B',
                'password' => Hash::make('87654321'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Juan Dela Cruz ',
                'section' => '2C',
                'password' => Hash::make('12341234'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('students')->insert($users);
    }
}
