<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Student;

class StudentController extends Controller
{

    public function index()
    {
        return view ('admin.admins.addStudents');
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
}
