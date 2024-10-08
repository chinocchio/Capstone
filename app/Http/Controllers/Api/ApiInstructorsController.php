<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Models\User_Subject;
use App\Models\Subject;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApiInstructorsController extends Controller
{
    public function index()
    {
        $instructors = User::get();
        if($instructors->count() > 0)
        {
            return UserResource::collection($instructors);
        }
        else
        {
            return response()->json(['message' => 'No subjects Recorde'], 200);
        }
    }
    public function store()
    {

    }

    public function show($pin)
    {
        // Check if the instructor exists
        $user = User::where('pin', $pin)->first();
    
        if (!$user) {
            return response()->json(['message' => 'Instructor not found'], 404);
        }
    
        // Get the current time and day in Asia/Manila timezone
        $now = Carbon::now('Asia/Manila');
        $currentDay = $now->format('l'); // Get the full name of the day (e.g., "Monday")
        $currentTime = $now->format('H:i:s'); // Get the current time in 24-hour format
    
        // Retrieve the instructor's details
        $instructor = $user->only(['id', 'username', 'email', 'finger_id']); // Customize as needed
    
        // Retrieve and filter the instructor's subjects based on current day and time
        $subjects = $user->subjects()
                         ->where('day', $currentDay)
                         ->where(function($query) use ($currentTime) {
                             $query->where('start_time', '<=', $currentTime)
                                   ->where('end_time', '>=', $currentTime);
                         })
                         ->get()
                         ->map(function($subject) use ($instructor) {
                             return array_merge($instructor, [
                                 'name' => $subject->name,
                                 'code' => $subject->code,
                                 'description' => $subject->description,
                                 'qr' => $subject->qr,
                                 'start_time' => $subject->start_time,
                                 'end_time' => $subject->end_time,
                                 'section' => $subject->section,
                                 'day' => $subject->day,
                             ]);
                         });
    
        // Format the response
        $response = $subjects->toArray();
        
        return response()->json($response, 200);
    }
    
    

    public function update(Request $request, $email)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'finger_id' => 'required|integer',
            'pin' => 'required|integer|digits:4',
            'fingerprint_template' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if the user with the provided email exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        // Check for duplicate pin
        $pinExists = User::where('pin', $request->pin)->where('id', '!=', $user->id)->exists();
        if ($pinExists) {
            return response()->json([
                'message' => 'Pin already exists.'
            ], 400);
        }

        // Update user data
        $user->finger_id = $request->finger_id;
        $user->pin = $request->pin;
        $user->fingerprint_template = $request->fingerprint_template;
        $user->save();

        // Return the updated user using the UserResource
        return new UserResource($user);
    }

    public function destroy()
    {
        
    }

    /**
     * Get user by PIN.
     *
     * @param  int  $pin
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByPin($pin)
    {
        // Validate the pin
        $validator = Validator::make(['pin' => $pin], [
            'pin' => 'required|integer|digits:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the user by PIN
        $user = User::where('pin', $pin)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        // Return the user data using the UserResource
        return new UserResource($user);
    }

    public function getByPinWithSubjects($pin, $day)
    {
        // Check if the instructor exists
        $user = User::where('pin', $pin)->first();

        if (!$user) {
            return response()->json(['message' => 'Instructor not found'], 404);
        }

        // Retrieve the instructor's details
        $instructor = $user->only(['id', 'username', 'email', 'finger_id']); // Customize as needed

        // Retrieve linked subjects for the given day
        $subjects = $user->subjects()
                         ->where('day', $day)
                         ->get();

        // Format the response
        $response = [
            'instructor' => $instructor,
            'subjects' => $subjects
        ];

        return response()->json($response, 200);
    }
    
}
