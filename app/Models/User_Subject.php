<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Subject extends Model
{
    use HasFactory;

    protected $table = 'user_subject';

    protected $fillable = [
        'user_id',
        'subject_id',
    ];
}
