<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    use HasFactory;

    protected $table = 'report'; // Specify the table name

    protected $fillable = [
        'from_email',
        'to_email',
        'subject',
        'message',
        'attachment_path',
        'status',
    ];
}

