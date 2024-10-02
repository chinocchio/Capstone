<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Student;
use Illuminate\Support\Facades\Session;

class StudentImport implements ToCollection, WithHeadingRow
{
    protected $duplicateRows;
    protected $headers; // Store headers dynamically

    public function __construct()
    {
        $this->duplicateRows = collect(); // Collect duplicate/error data
        $this->headers = [];  // Store the headers from the incoming Excel
    }

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        // Get headers from the first row (heading row)
        if ($rows->isNotEmpty()) {
            $this->headers = array_keys($rows->first()->toArray());
        }

        foreach ($rows as $row) 
        {
            // Check if the student already exists based on student_number or email
            $existingStudent = Student::where('student_number', $row['student_number'])
                ->orWhere('email', $row['email'])
                ->first();

            if ($existingStudent || empty($row['student_number']) || empty($row['email'])) {
                // If a duplicate or error, add to the collection
                $this->duplicateRows->push($row);
            } else {
                // Insert the new student
                Student::create([
                    'student_number' => $row['student_number'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'section' => $row['section'],
                ]);
            }
        }

        // Store the duplicate/error data and the headers in the session
        Session::put('duplicate_students', $this->duplicateRows);
        Session::put('import_headers', $this->headers);  // Store headers dynamically
    }
}
