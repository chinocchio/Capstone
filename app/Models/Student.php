<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MAac;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'name',
        'email',
        'section',
        'password',
        'biometric_data'
    ];

    // public function macs()
    // {
    //     return $this->belongsTo(Mac::class);
    // }

    // Define the many-to-many relationship with the Mac model
    public function macs()
    {
        return $this->belongsToMany(Mac::class, 'mac_student')
                        ->withTimestamps();
    }
}
