<?php

namespace App\Exports;

use App\Models\Logs;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Logs::with('user') // Assuming you have a user relationship on the Log model
            ->get()
            ->map(function ($log) {
                // Add the current date and time in/out fields
                $currentDate = Carbon::now()->format('Y-m-d');
                $timeIn = $this->parseTime($log->scanned_at);  // Assuming 'scanned_at' as Time In
                $timeOut = $this->parseTime($log->verified_at); // Assuming 'verified_at' as Time Out

                return [
                    'User' => $log->user ? $log->user->name : 'Admin', // Get user name if available, otherwise default to 'Admin'
                    'Status' => $log->status,
                    'Time In' => $timeIn,
                    'Time Out' => $timeOut,
                    'Day' => $log->day,
                    'Log Date' => $log->created_at->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'User',
            'Status',
            'Time In',
            'Time Out',
            'Day',
            'Log Date',
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
