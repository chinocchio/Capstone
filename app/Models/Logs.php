<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = "logs";

    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'time',
        'day',
    ];
}
