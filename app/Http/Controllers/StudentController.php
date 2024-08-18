<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;

class StudentController extends Controller
{
    //API 
    public function verifyStudent (Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|digits:8',
        ]);

        $user = Student::where('name', $request->name)->first();

        // dd($user);


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
}
