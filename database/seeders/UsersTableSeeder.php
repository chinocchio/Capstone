<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed the users table.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'username' => 'Angeline Escuro ',
                'email' => 'an@my.cspc.edu.ph',
                'password' => Hash::make('1111'),
                'google_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Joshua Secundo ',
                'email' => 'jo@my.cspc.edu.ph',
                'password' => Hash::make('1234'),
                'google_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Boyet Penales ',
                'email' => 'bo@my.cspc.edu.ph',
                'password' => Hash::make('2222'),
                'google_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
