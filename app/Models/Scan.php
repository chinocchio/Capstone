<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Scan extends Model
{
    use HasFactory;

    protected $table = "scans";
    
    protected $fillable = [
        'subject_id', 
        'scanned_by', 
        'scanned_at'
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

        // Define the relationship with the Student model
        public function student()
        {
            // Assuming 'scanned_by' is the student's name and matches the 'name' column in the students table
            return $this->belongsTo(Student::class, 'scanned_by', 'name');
        }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
