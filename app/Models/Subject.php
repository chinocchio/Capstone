<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'name',
        'code',
        'description',
        'section',
        'start_time',
        'end_time',
        'image',
    ];

    // protected $casts = [
    //     'start_time' => 'datetime:H:i',
    //     'end_time' => 'datetime:H:i',
    // ];

    public function users()
    {
        // return $this->belongsToMany(User::class, 'user_subject');
        return $this->belongsToMany(User::class, 'user_subject', 'subject_id', 'user_id');
    }
}
