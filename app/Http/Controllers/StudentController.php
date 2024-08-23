<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Student;

class StudentController extends Controller
{

    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('id', 'like', "%{$searchTerm}%");
        }

        $students = $query->paginate(5);
        return view ("admin.admins.addStudents", compact('students'));
    }

    //API 
    public function verifyStudent (Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|digits:8',
        ]);

        $user = Student::where('name', $request->name)->first();

        // Check if the user exists and the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid username or password',
            ], 401);
        }


        // If login is successful, return the user data
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'section' => $user->section,
        ]);
    }

    public function finger(Request $request)
    {
        $request->validate([
            
        ]);
    }

    //Import Student
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        Excel::import(new StudentImport, $request->file('file'));

        return redirect()->back()->with('success', 'Subjects imported successfully!');
    }


    public function registerBiometrics(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'biometric_data' => 'required|string',
        ]);

        // Save the biometric data in the database
        $user = Student::find($request->user_id);
        if ($user) {
            $user->biometric_data = $request->biometric_data; // Store the biometric data
            $user->save();

            return response()->json(['message' => 'Biometrics registered successfully'], 200);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    //API with fingerprint
    /**
     * Display a listing of the students.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllStudent()
    {
        $students = Student::all(['id', 'name', 'email', 'section', 'biometric_data']); // Specify fields to retrieve

        // Encode biometric data to Base64 if it exists
        $students->transform(function ($student) {
            if ($student->biometric_data) {
                $student->biometric_data = base64_encode($student->biometric_data);
            }
            return $student;
        });

        return response()->json($students);
    }

    /**
     * Find the student by biometric data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByBiometricData(Request $request)
    {
        $request->validate([
            'biometric_data' => 'required|string',
        ]);

        $biometricData = $request->input('biometric_data');

        // Decode Base64-encoded biometric data to binary
        $biometricDataBinary = base64_decode($biometricData);

        // Query to find the student by binary biometric data
        $student = Student::where('biometric_data', $biometricDataBinary)->first();

        if ($student) {
            return response()->json([
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'section' => $student->section,
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => 'No student found with the provided biometric data',
        ], Response::HTTP_NOT_FOUND);
    }

     /**
     * Store a newly created student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeStudent(Request $request)
    {
        dd($request);
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students',
            'section' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'biometric_data' => 'nullable|string', // Use string for binary data
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        // Process biometric data if present
        $biometricData = $request->input('biometric_data');
        if ($biometricData) {
            // If you are using base64 encoding, decode it before saving
            $biometricData = base64_decode($biometricData);
        }

        // Create a new student record
        $student = Student::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'section' => $request->input('section'),
            'password' => Hash::make($request->input('password')),
            'biometric_data' => $biometricData, // Save binary data
        ]);

        return response()->json($student, Response::HTTP_CREATED);
    }

}
