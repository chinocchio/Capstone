<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scan;
use App\Models\Subject;

class ScansController extends Controller
{

    public function recordScan(Request $request)
    {

        $validatedData = $request->validate([
            'qr' => 'required|string',
            'scanned_by' => 'required|string',
        ]);
    
        // Find the subject by QR code
        $subject = Subject::where('qr', $validatedData['qr'])->firstOrFail();
    
        // Record the scan
        $scan = Scan::create([
            'subject_id' => $subject->id,
            'scanned_by' => $validatedData['scanned_by'],
        ]);
    
        return response()->json(['message' => 'Scan recorded successfully', 'scan' => $scan], 201);
    }
}
