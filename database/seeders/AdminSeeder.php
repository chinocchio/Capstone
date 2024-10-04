<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Setting;
use Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obj = new Admin;
        $obj->username = "admin";
        $obj->password = Hash::make("admin12345");
        $obj->save();

        $setting = new Setting;
        $setting->academic_year = '2024-2025';
        $setting->current_semester = '1st Semester';
        $setting->save();
    }
}
