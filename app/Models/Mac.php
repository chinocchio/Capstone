<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;

class Mac extends Model
{
    use HasFactory;

    protected $table = 'macs';

    protected $fillable = [
        'mac_number',
        'qr',
    ];

    // public function student()
    // {
    //     return $this->hasMany(Student::class);
    // }

    // Define the many-to-many relationship with the Student model
    public function students()
    {
        return $this->belongsToMany(Student::class, 'mac_student')
                    ->withTimestamps();
    }
}
