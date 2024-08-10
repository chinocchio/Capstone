<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Subject;
use Carbon\Carbon;

class SubjectsTableSeeder extends Seeder
{
    /**
     * Seed the subjects table.
     *
     * @return void
     */
    public function run()
    {
        $subjects = [
            [
                'name' => 'Mobile Technology 1',
                'code' => 'ITEC 222',
                'description' => 'Introduction to React Native and making simple projects.',
                'section' => '2D',
                'start_time' => Carbon::createFromFormat('g:i A', '1:00 PM')->format('H:i:s'),
                'end_time' => Carbon::createFromFormat('g:i A', '4:00 PM')->format('H:i:s'),
            ],
            [
                'name' => 'Mobile Technology 1',
                'code' => 'ITEC 222',
                'description' => 'Introduction to React Native and making simple projects.',
                'section' => '2C',
                'start_time' => Carbon::createFromFormat('g:i A', '11:00 AM')->format('H:i:s'),
                'end_time' => Carbon::createFromFormat('g:i A', '12:30 PM')->format('H:i:s'),
            ],
            [
                'name' => 'Multi Meida 1',
                'code' => 'IT 222',
                'description' => 'Introduction to Editng and Photoshop',
                'section' => '2D',
                'start_time' => Carbon::createFromFormat('g:i A', '10:00 AM')->format('H:i:s'),
                'end_time' => Carbon::createFromFormat('g:i A', '12:30 PM')->format('H:i:s'),
            ],
            [
                'name' => 'Application and Development',
                'code' => 'ICT 242',
                'description' => 'Introduction to Laravel and making simple projects.',
                'section' => '2C',
                'start_time' => Carbon::createFromFormat('g:i A', '11:00 AM')->format('H:i:s'),
                'end_time' => Carbon::createFromFormat('g:i A', '12:30 PM')->format('H:i:s'),
            ],
            [
                'name' => 'Mobile Technology 2',
                'code' => 'ITEC 223',
                'description' => 'Introduction to React Native and making intermidiate projects.',
                'section' => '2A',
                'start_time' => Carbon::createFromFormat('g:i A', '7:00 AM')->format('H:i:s'),
                'end_time' => Carbon::createFromFormat('g:i A', '10:00 AM')->format('H:i:s'),
            ],
            
        ];

        // Insert sample data into the subjects table
        DB::table('subjects')->insert($subjects);
    }
}
