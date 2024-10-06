<?php

namespace App\Http\Controllers;

use App\Models\Mac_Student;
use App\Models\StudentSubject;
use App\Models\UserSubject;
use App\Models\Logs;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogsController extends Controller
{
    /**
     * Display the status of the latest log entry.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve the latest log entry
        $latestLog = Logs::latest('created_at')->first();

        // Check if a log entry exists
        if (!$latestLog) {
            return response()->json(['status' => 'No logs found'], Response::HTTP_NOT_FOUND);
        }

        // Return the status of the latest log entry
        return response()->json(['status' => $latestLog->status]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

   /**
     * Store a newly created log in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',  // Allow null for user_id
            'status' => 'required|string|max:255',
            'time' => 'required|date_format:H:i',
            'day' => 'required|string|max:255',
        ]);

        // Create a new log entry
        $log = Logs::create($validated);

        // Return a response indicating success
        return response()->json($log, Response::HTTP_CREATED);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function dataRecords()
    {
        // Fetch all attendance records
        $studentAttendance = DB::table('student_attendance')
            ->join('students', 'student_attendance.student_id', '=', 'students.id')
            ->leftJoin('mac_student', 'student_attendance.student_id', '=', 'mac_student.student_id') // Fetch PC number, if exists
            ->join('user_subject', 'student_attendance.subject_id', '=', 'user_subject.subject_id')
            ->join('users', 'user_subject.user_id', '=', 'users.id')
            ->select(
                'student_attendance.id as attendance_id',
                'students.name as student_name',
                'students.student_number',
                'students.section as year_course',
                'mac_student.mac_id as pc_number',
                'users.username as instructor_name',
                'student_attendance.time_in',
                'student_attendance.date'
            )
            ->orderBy('student_attendance.date', 'desc') // Order by date, latest first
            ->get();
    
        // Pass the retrieved data to the view
        return view('admin.admins.dataViewer', compact('studentAttendance'));
    }
    
    
    
}
