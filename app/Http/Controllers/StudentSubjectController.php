<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\Validator;

class StudentSubjectController extends Controller
{
    public function index()
    {
        // Fetch all student-subject relationships with related student and subject details
        $studentSubjects = Student::with('subjects')->get();

        return response()->json($studentSubjects);
    }
    
    /**
     * Store a newly created student-subject association in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        // Retrieve the validated data
        $data = $validator->validated();

        // Check if the association already exists
        $existingAssociation = Student::find($data['student_id'])
            ->subjects()
            ->where('subject_id', $data['subject_id'])
            ->exists();

        if ($existingAssociation) {
            return response()->json([
                'status' => 'error',
                'message' => 'This student is already associated with the selected subject.',
            ], 400);
        }

        // Attach the subject to the student
        $student = Student::find($data['student_id']);
        $student->subjects()->attach($data['subject_id']);

        return response()->json([
            'status' => 'success',
            'message' => 'Student successfully associated with the subject.',
        ], 201);
    }

    public function show($studentId)
    {
        // Validate the student ID
        $student = Student::find($studentId);

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found.',
            ], 404);
        }

        // Get the subjects associated with the student
        $subjects = $student->subjects;

        return response()->json([
            'status' => 'success',
            'data' => $subjects,
        ], 200);
    }
}

