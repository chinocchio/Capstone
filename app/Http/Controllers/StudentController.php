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
                ->orWhere('student_number', 'like', "%{$searchTerm}%");
        }

        $students = $query->paginate(15);
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

        return redirect()->back()->with('success', 'Students imported successfully!');
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view ('admin.admins.createStudent');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'student_number' => 'required|unique:students,student_number',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'section' => 'required|string|max:255',
            'biometric_data' => 'nullable|file',
        ]);

        // Handle file upload for biometric data, if provided
        $biometricData = null;
        if ($request->hasFile('biometric_data')) {
            $biometricData = file_get_contents($request->file('biometric_data')->getRealPath());
        }

        // Create a new student
        $student = new Student();
        $student->student_number = $request->student_number;
        $student->name = $request->name;
        $student->email = $request->email;
        $student->section = $request->section;
        $student->biometric_data = $biometricData;
        $student->save();

        // Redirect back with a success message
        return redirect()->route('student_view')->with('success', 'Student added successfully.');
    }

        // Method to show the edit form
        public function edit($id)
        {
            $student = Student::findOrFail($id);
            return view('admin.admins.editStudent', compact('student'));
        }
    
        // Method to update the student's data
        public function update(Request $request, $id)
        {
            // Validate the incoming request data
            $request->validate([
                'student_number' => 'required|unique:students,student_number,' . $id,
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:students,email,' . $id,
                'section' => 'required|string|max:255',
                'biometric_data' => 'nullable|file',
            ]);
    
            // Find the student by ID
            $student = Student::findOrFail($id);
    
            // Handle file upload for biometric data, if provided
            if ($request->hasFile('biometric_data')) {
                $student->biometric_data = file_get_contents($request->file('biometric_data')->getRealPath());
            }
    
            // Update the student's data
            $student->student_number = $request->student_number;
            $student->name = $request->name;
            $student->email = $request->email;
            $student->section = $request->section;
            $student->save();
    
            // Redirect back with a success message
            return redirect()->route('student_view')->with('success', 'Student updated successfully.');
        }
    
        // Method to delete a student
        public function destroy($id)
        {
            // Find the student by ID and delete
            $student = Student::findOrFail($id);
            $student->delete();
    
            // Redirect back with a success message
            return redirect()->route('student_view')->with('delete', 'Student deleted successfully.');
        }

        public function getBiometricData($id)
        {
            // Fetch the student by ID
            $student = Student::find($id);
    
            // Check if the student exists and has biometric data
            if (!$student) {
                return response()->json(['status' => 'fail', 'message' => 'Student not found'], 404);
            }
    
            if (!$student->biometric_data) {
                return response()->json(['status' => 'fail', 'message' => 'No biometric data available for this student'], 404);
            }
    
            // Return the biometric data in a JSON response
            return response()->json([
                'status' => 'success',
                'biometric_data' => base64_encode($student->biometric_data), // Encoding binary data to base64
                'student' => [
                    'name' => $student->name,
                    'id' => $student->id,
                ]
            ]);
        }

        public function register(Request $request)
        {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'biometric_data' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Find the user by username
            $student = Student::where('name', $request->input('username'))->first();

            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }

            // Save the biometric data (fingerprint template)
            $student->biometric_data = base64_decode($request->input('biometric_data'));
            $student->save();

            return response()->json(['message' => 'Biometric data registered successfully'], 200);
        }



        public function verify(Request $request)
        {
            // Validate the request to ensure biometric_data is provided
            $validator = Validator::make($request->all(), [
                'biometric_data' => 'required|string',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
        
            // Decode the incoming biometric data
            $incomingBiometricData = base64_decode($request->input('biometric_data'));
        
            // Retrieve all students with biometric data
            $students = Student::whereNotNull('biometric_data')->get();
        
            // Placeholder for result
            $verified = false;
            $username = null;
        
            // Iterate over each stored template and compare
            foreach ($students as $student) {
                // Retrieve and decode stored biometric data
                $storedBiometricData = $student->biometric_data;
        
                // Compare the stored biometric data with the incoming biometric data
                if ($this->compareBiometricData($storedBiometricData, $incomingBiometricData)) {
                    $verified = true;
                    $username = $student->name;
                    break; // Exit loop on first match
                }
            }
        
            // Return the result of the verification
            return response()->json([
                'verified' => $verified,
                'username' => $verified ? $username : null,
            ], 200);
        }
        
        // Placeholder for biometric comparison logic
        private function compareBiometricData($storedData, $incomingData)
        {
            // Implement actual comparison logic
            return $storedData === $incomingData;
        }
        


        public function getTemplate(Request $request)
        {
            // Retrieve the student based on some identifier (e.g., ID, username, etc.)
            $student = Student::where('name', $request->input('username'))->first();

            if ($student && $student->biometric_data) {
                return response()->json([
                    'biometric_data' => base64_encode($student->biometric_data),
                    'username' => $student->name,
                ], 200);
            }

            return response()->json(['error' => 'Biometric data not found'], 404);
        }




}
