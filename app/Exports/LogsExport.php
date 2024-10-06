<?php

namespace App\Exports;

use App\Models\StudentAttendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class LogsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Fetch all attendance records
        return DB::table('student_attendance')
            ->join('students', 'student_attendance.student_id', '=', 'students.id')
            ->leftJoin('mac_student', 'student_attendance.student_id', '=', 'mac_student.student_id') // Fetch PC number, if exists
            ->join('user_subject', 'student_attendance.subject_id', '=', 'user_subject.subject_id')
            ->join('users', 'user_subject.user_id', '=', 'users.id')
            ->select(
                'student_attendance.date',
                'students.name as student_name',
                'mac_student.mac_id as pc_number',
                'students.student_number',
                'students.section as year_course',
                'users.username as instructor_name',
                'student_attendance.time_in'
            )
            ->orderBy('student_attendance.date', 'desc') // Order by date, latest first
            ->get();
    }

    // Define the headings for the exported Excel file
    public function headings(): array
    {
        return [
            'Date',
            'Name',
            'PC #',
            'Student ID #',
            'Year Course & Section',
            'Instructor',
            'Time In',
        ];
    }

    // Map the data for each row in the export
    public function map($attendance): array
    {
        return [
            $attendance->date,
            $attendance->student_name,
            $attendance->pc_number ?? 'N/A', // Show 'N/A' if no PC is linked
            $attendance->student_number,
            $attendance->year_course,
            $attendance->instructor_name,
            $attendance->time_in,
        ];
    }
}
