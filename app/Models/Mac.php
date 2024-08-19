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
        'name',
        'section',
        'password',
    ];

    public function student()
    {
        return $this->hasMany(Student::class);
    }
}
