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


class ScansController extends Controller
{
    public function getLocation()
    {
        $location = Location::get();

        if ($location) {
            return response()->json([
                'ip' => $location->ip,
                'country' => $location->countryName,
                'region' => $location->regionName,
                'city' => $location->cityName,
                'zip' => $location->zipCode,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
            ]);
        } else {
            return response()->json([
                'message' => 'Location could not be determined',
            ], 404);
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
    //      $qrData = $request->input('qr'); // QR code data
    //      $studentId = $request->input('student_id'); // Student ID
 
    //      // Retrieve the MAC address using the QR code data
    //      $mac = Mac::where('qr', $qrData)->first();
 
    //      if ($mac) {
    //          // Find the student based on the student ID
    //          $student = Student::find($studentId);
 
    //          if ($student) {
    //              // Check if the QR code has already been scanned by another student in the same section
    //              $existingScan = $mac->students()
    //                                  ->where('section', $student->section)
    //                                  ->first();
 
    //              if ($existingScan) {
    //                  // QR code already scanned by a student in the same section
    //                  return response()->json([
    //                      'message' => 'This QR code has already been scanned by a student in your section. Please scan a different QR code.'
    //                  ], 400);
    //              }
 
    //              // Attach the MAC address to the student
    //              $student->macs()->attach($mac->id);
 
    //              return response()->json(['message' => 'MAC address linked to student successfully.']);
    //          } else {
    //              return response()->json(['message' => 'Student not found.'], 404);
    //          }
    //      } else {
    //          return response()->json(['message' => 'MAC address not found.'], 404);
    //      }
    {
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

        // Retrieve the student and validate
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

        // Check if a student from this section is already linked to the MAC
        $existingLink = Mac_Student::where('mac_id', $mac->id)
            ->join('students', 'mac_student.student_id', '=', 'students.id')
            ->where('students.section', $student->section)
            ->exists();

        if ($existingLink) {
            return response()->json([
                'success' => false,
                'message' => 'A student from this section is already linked to this MAC'
            ], 400);
        }

        // Insert the data into the mac_student pivot table
        $macStudent = Mac_Student::create([
            'student_id' => $request->student_id,
            'mac_id' => $mac->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Record successfully created',
            'data' => $macStudent
        ], 201);
        }
    }
}
}
