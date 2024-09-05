<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class StudentImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            Student::create([
                'student_number' => $row['student_number'],
                'name' => $row['name'],
                'email' => $row['email'],
                'section' => $row['section'],
                'password' => Hash::make($row['password']),
            ]);
        }
    }
}
