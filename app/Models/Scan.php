<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Scan extends Model
{
    use HasFactory;

    protected $table = "scans";
    
    protected $fillable = [
        'subject_id', 
        'scanned_by', 
        'scanned_at'
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
