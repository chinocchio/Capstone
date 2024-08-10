<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'name',
        'code',
        'description',
        'section',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'image',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_subject');
    }
}
