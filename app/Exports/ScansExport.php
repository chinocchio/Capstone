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
        return Scan::with(['subject', 'student.macs'])
            ->get()
            ->map(function ($scan) {
                $linkedMacs = $scan->student ? $scan->student->macs->pluck('mac_address')->implode(', ') : 'N/A';

                // Convert 'time' fields to Carbon instances using today's date if possible
                $scannedAt = $this->parseTime($scan->scanned_at);
                $verifiedAt = $this->parseTime($scan->verified_at);

                return [
                    'name' => $scan->scanned_by,
                    'section' => $scan->student->section ?? 'N/A',
                    'linked_macs' => $linkedMacs,
                    'time_in' => $scannedAt,
                    'time_out' => $verifiedAt,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Section',
            'Linked MAC PCs',
            'Time In',
            'Time Out',
        ];
    }

    /**
     * Parse the time field to a readable format, handling potential errors.
     *
     * @param string|null $time
     * @return string
     */
    private function parseTime($time)
    {
        try {
            // Attempt to parse using a strict time format
            return Carbon::createFromFormat('H:i:s', $time)->format('h:i a');
        } catch (\Exception $e) {
            // Fallback to a more flexible parse if the strict format fails
            try {
                return Carbon::parse($time)->format('h:i a');
            } catch (\Exception $e) {
                // Return 'Invalid Time' if parsing fails completely
                return 'Invalid Time';
            }
        }
    }
}



