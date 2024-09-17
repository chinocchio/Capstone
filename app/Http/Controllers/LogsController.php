<?php

namespace App\Http\Controllers;

use App\Models\Mac_Student;
use App\Models\StudentSubject;
use App\Models\UserSubject;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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
        // Fetch data from the student_subject table using DB facade
        $studentSubjects = DB::table('student_subject')
        ->join('students', 'student_subject.student_id', '=', 'students.id')
        ->join('subjects', 'student_subject.subject_id', '=', 'subjects.id')
        ->select('student_subject.id', 'students.name as student_name', 'subjects.name as subject_name', 'student_subject.created_at')
        ->get();

        // You can still fetch other data from different tables if necessary
        $macStudents = DB::table('mac_student')
        ->join('students', 'mac_student.student_id', '=', 'students.id')
        ->select('mac_student.id', 'students.name as student_name', 'mac_student.mac_id', 'mac_student.created_at')
        ->get();

        $logs = DB::table('logs')
        ->leftJoin('users', 'logs.user_id', '=', 'users.id')
        ->select('logs.*', 'users.username as user_name')
        ->get();


        // Pass the data to the view
        return view('admin.admins.dataViewer', compact('studentSubjects', 'macStudents', 'logs'));
    }
}
