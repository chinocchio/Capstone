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
                'section' => 'BSIT 2H',
                'student_number' => '635643456',
                'email' => 'ch@my.cspc.edu.ph',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Edward Sarte ',
                'section' => 'BSIT 2B',
                'student_number' => '612421456',
                'email' => 'ed@my.cspc.edu.ph',
                'password' => Hash::make('87654321'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Juan Dela Cruz ',
                'section' => 'BSIT 2C',
                'student_number' => '1241246',
                'email' => 'ju@my.cspc.edu.ph',
                'password' => Hash::make('12341234'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('students')->insert($users);
    }
}
