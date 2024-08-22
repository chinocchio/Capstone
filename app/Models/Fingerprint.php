<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fingerprint extends Model
{
    use HasFactory;

    protected $table = "fingerprints";

    protected $fillable = [
        'fname',
        'pin',
        'finger_print',
    ];

    // Optionally, you can use `hidden` to hide binary data in JSON responses
    // protected $hidden = [
    //     'finger_print',
    // ];
    
}
