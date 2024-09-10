<?php

namespace App\Imports;

use App\Models\Subject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\ValidationException;

class SubjectImport implements ToCollection, WithHeadingRow
{
    private $duplicateSubjects = [];
    private $schoolYear;
    private $semester;

    public function __construct($schoolYear, $semester)
    {
        $this->schoolYear = $schoolYear;
        $this->semester = $semester;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Check for duplicates based on day, section, school_year, and semester
            $existingSubject = Subject::where('day', $row['day'])
                                      ->where('section', $row['section'])
                                      ->where('school_year', $this->schoolYear)
                                      ->where('semester', $this->semester)
                                      ->first();

            // If a duplicate exists, skip it and add to duplicateSubjects array
            if ($existingSubject) {
                $this->duplicateSubjects[] = [
                    'code' => $row['code'],
                    'day' => $row['day'],
                    'section' => $row['section'],
                ];
                continue;
            }

            // Generate a QR code or use the existing one
            $generatedCode = mt_rand(11111111111,99999999999);

            // Create the subject with the provided school_year and semester
            Subject::create([
                'name' => $row['name'],
                'code' => $row['code'],
                'description' => $row['description'],
                'section' => $row['section'],
                'qr' => $row['qr'] ?? $generatedCode,
                'start_time' => $this->formatTime($row['start_time']),
                'end_time' => $this->formatTime($row['end_time']),
                'day' => $row['day'],
                'image' => $row['image'], // Ensure the index matches the actual column if used
                'school_year' => $this->schoolYear,
                'semester' => $this->semester,
            ]);
        }
    }

    public function getDuplicateSubjects()
    {
        return $this->duplicateSubjects;
    }

    private function formatTime($decimal)
    {
        // Check if the value is null or empty
        if (is_null($decimal)) {
            return null;
        }

        // Convert decimal to hours, minutes, and seconds
        $hours = floor($decimal * 24);
        $minutes = floor(($decimal * 24 - $hours) * 60);
        $seconds = floor((($decimal * 24 - $hours) * 60 - $minutes) * 60);

        // Format and return the time string in 'H:i:s' format
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

}
