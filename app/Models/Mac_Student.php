<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mac_Student extends Model
{
    use HasFactory;

    protected $table = 'mac_student';

    protected $fillable = [
        'student_id',
        'mac_id',
    ];
}
