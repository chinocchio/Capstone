<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StudentAttendance extends Model
{
    use HasFactory;

    // Define the table (optional if it follows Laravel convention)
    protected $table = 'student_attendance';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'student_id', 'subject_id', 'status', 'time_in', 'time_out', 'date', 'school_year', 'semester'
    ];

    // Define the relationship with the Student model
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Define the relationship with the Subject model
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}

