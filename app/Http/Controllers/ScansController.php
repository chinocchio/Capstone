<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Scan;
use App\Models\Subject;
use App\Models\Mac;
use App\Models\Student;
use App\Models\Mac_Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Http;


class ScansController extends Controller
{

    public function updateStatus(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'username' => 'required|string', // The name of the user whose fingerprint was verified
            'fingerprint_verified' => 'required|boolean',
        ]);

        // Find the scan entry by 'username' (adjust this to match your database schema)
        $scan = Scan::where('scanned_by', $validatedData['username']) // Assuming 'scanned_by' stores the username
                    ->where('fingerprint_verified', false) // Ensure you're updating only unverified scans
                    ->orderBy('created_at', 'desc')
                    ->firstOrFail();

        // Update the fingerprint_verified status
        $scan->fingerprint_verified = $validatedData['fingerprint_verified'];
        $scan->save();

        // Return a success response
        return response()->json(['message' => 'Fingerprint verification status updated successfully'], 200);
    }


    public function getLocation()
    {
        // Specific IP address
        $ip = '122.3.156.198';
        
        // Replace with your actual IPregistry API key
        $apiKey = 'pid2gc6x3r0bw739';

        // Send a GET request to the IPregistry API
        $response = Http::get("https://api.ipregistry.co/{$ip}?key={$apiKey}");

        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json();

            $customLatitude = 13.4056369;
            $customLongitude = 123.3771934;

            return response()->json([
                'ip' => $data['ip'],
                'country' => $data['location']['country']['name'],
                'region' => $data['location']['region']['name'],
                'city' => $data['location']['city'],
                'isp' => $data['connection']['organization'],
                'organization' => $data['connection']['organization'],
                'latitude' => $customLatitude,
                'longitude' => $customLongitude,
            ]);
        } else {
            return response()->json([
                'message' => 'Location could not be determined',
            ], 500);
        }
    }

    public function recordScan(Request $request)
    {
        $validatedData = $request->validate([
            'qr' => 'required|string',
            'scanned_by' => 'required|string',
        ]);
    
        // Find the subject by QR code
        $subject = Subject::where('qr', $validatedData['qr'])->firstOrFail();
    
        // Record the scan with Asia/Manila timezone
        $scan = Scan::create([
            'subject_id' => $subject->id,
            'scanned_by' => $validatedData['scanned_by'],
            'scanned_at' => Carbon::now()->setTimezone('Asia/Manila')->format('H:i:s'),  // Store only the time
            'created_at' => Carbon::now()->setTimezone('Asia/Manila'),
            'updated_at' => Carbon::now()->setTimezone('Asia/Manila'),
        ]);
    
        return response()->json(['message' => 'Scan recorded successfully', 'scan' => $scan], 201);
    }

    

    /**
     * Get the list of scans.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getScans(Request $request): JsonResponse
    {
        // Optionally, you can add filters here based on query parameters
        $query = Scan::query();

        // Apply filters if provided
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }
        if ($request->has('scanned_by')) {
            $query->where('scanned_by', $request->input('scanned_by'));
        }

        // Fetch all records or apply pagination
        $scans = $query->get(['subject_id', 'scanned_by', 'scanned_at', 'created_at', 'updated_at']);

        return response()->json(['scans' => $scans]);
    }

    /**
     * Get the current time and day in 12-hour format.
     *
     * @return JsonResponse
     */
    public function get12HourFormat(): JsonResponse
    {
        // Get the current time in Asia/Manila timezone
        $currentTime = Carbon::now('Asia/Manila');
        
        // Format the time in 12-hour format with AM/PM
        $formattedTime = $currentTime->format('g:i A');
        
        // Get the current day of the week
        $dayOfWeek = $currentTime->format('l');
        
        // Return the formatted time and day as a JSON response
        return response()->json([
            'time' => $formattedTime,
            'day' => $dayOfWeek,
        ]);
    }

    /**
     * Get the current time in 24-hour format.
     *
     * @return JsonResponse
     */
    public function get24HourFormat(): JsonResponse
    {
        // Get the current time in Asia/Manila timezone
        $currentTime = Carbon::now('Asia/Manila');
        
        // Format the time in 24-hour format
        $formattedTime = $currentTime->format('H:i');

                // Get the current day of the week
                $dayOfWeek = $currentTime->format('l');

        return response()->json([
            'time' => $formattedTime,
            'day' => $dayOfWeek,
        ]);
    }




     //link mac to student
     public function linkToStudent(Request $request)
     {
         // Validate the incoming request
         $validator = Validator::make($request->all(), [
             'student_id' => 'required|exists:students,id',
             'qr' => 'required|exists:macs,qr',
         ]);
     
         if ($validator->fails()) {
             return response()->json([
                 'success' => false,
                 'message' => 'Validation failed',
                 'errors' => $validator->errors()
             ], 400);
         }
     
         // Retrieve the student
         $student = Student::find($request->student_id);
         if (!$student) {
             return response()->json([
                 'success' => false,
                 'message' => 'Student not found'
             ], 404);
         }
     
         // Retrieve the MAC ID using the QR code
         $mac = Mac::where('qr', $request->qr)->first();
         if (!$mac) {
             return response()->json([
                 'success' => false,
                 'message' => 'MAC not found for the given QR code'
             ], 404);
         }
     
         // Remove any existing MAC association for the student
         $student->macs()->detach();
     
         // Insert the new MAC association
         $macStudent = Mac_Student::create([
             'student_id' => $request->student_id,
             'mac_id' => $mac->id,
         ]);
     
         return response()->json([
             'success' => true,
             'message' => 'MAC successfully linked to student',
             'data' => $macStudent
         ], 201);
     }
}
