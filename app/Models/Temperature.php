<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temperature extends Model
{
    protected $table = 'temperature';
    
    use HasFactory;

    protected $fillable = ['temperature', 'humidity'];
}
