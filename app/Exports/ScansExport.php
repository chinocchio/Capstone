<?php

namespace App\Exports;

use App\Models\Scan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ScansExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Scan::with(['subject', 'student'])
            ->get()
            ->map(function ($scan) {
                // Get the current date
                $currentDate = Carbon::now()->format('Y-m-d');

                // Determine if the student is present or absent
                $status = $scan->verified_at ? 'Present' : 'Absent';

                return [
                    'name' => $scan->scanned_by,
                    'section' => $scan->student->section ?? 'N/A',
                    'current_date' => $currentDate,
                    'status' => $status,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Section',
            'Date',
            'Status (Present/Absent)',
        ];
    }
}
