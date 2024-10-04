<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InstructorsImport implements ToCollection, WithHeadingRow
{
    protected $schoolYear;
    protected $semester;
    public $duplicates = [];

    // Constructor to pass school_year and semester
    public function __construct($schoolYear, $semester)
    {
        $this->schoolYear = $schoolYear;
        $this->semester = $semester;
    }

    /**
     * Process each row in the Excel sheet
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            // Check if the email ends with "@my.cspc.edu.ph"
            if (!str_ends_with($row['email'], '@my.cspc.edu.ph')) {
                continue; // Skip the row if email is not valid
            }

            // Check if the instructor already exists for the same semester
            $existingInstructor = User::where('instructor_number', $row['instructor_number'])
                                    ->where('email', $row['email'])
                                    ->where('semester', $this->semester)
                                    ->first();

            if ($existingInstructor) {
                // Log duplicates
                $this->duplicates[] = [
                    'instructor_number' => $row['instructor_number'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'semester' => $this->semester,
                ];
                continue; // Skip duplicates for the same semester
            }

            // If no duplicate for this semester, create the instructor entry
            User::create([
                'instructor_number' => $row['instructor_number'],
                'username' => $row['username'],
                'email' => $row['email'],
                'school_year' => $this->schoolYear, // Assign the correct school_year
                'semester' => $this->semester, // Assign the correct semester
            ]);
        }
    }

    // Return duplicates to be displayed in the session message
    public function getDuplicates()
    {
        return $this->duplicates;
    }
}
