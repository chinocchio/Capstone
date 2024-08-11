<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instructors = User::all();
        return view('admin.instructors.index', compact('instructors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return view for creating a new user
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate and store a new user
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Display details of a specific user
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($instructorId)
    {
        $instructor = User::findOrFail($instructorId);

        // Subjects currently linked to the user
        $linkedSubjects = $instructor->subjects;

        // All subjects that are not linked to any user
        $availableSubjects = Subject::whereDoesntHave('users')->get();

        return view('admin.admins.aEdit', compact('instructor', 'linkedSubjects', 'availableSubjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $instructorId)
    {
        $instructor = User::findOrFail($instructorId);
        $instructor->update($request->all());
        return redirect()->route('admin_dashboard');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::destroy($id);
        return redirect()->route('admin_dashboard');
    }

    /**
     * Display user dashboard with linked and available subjects.
     */
    public function showDashboard()
    {
        $user = Auth::user();
        
        // Subjects currently linked to the user
        $linkedSubjects = $user->subjects->map(function($subject) {
            $subject->start_time = Carbon::parse($subject->start_time);
            $subject->end_time = Carbon::parse($subject->end_time);
            return $subject;
        });

        // All subjects that are not linked to any user
        $availableSubjects = Subject::whereDoesntHave('users')->get()->map(function($subject) {
            $subject->start_time = Carbon::parse($subject->start_time);
            $subject->end_time = Carbon::parse($subject->end_time);
            return $subject;
        });

        return view('users.subjects', compact('linkedSubjects', 'availableSubjects'));
    }

    /**
     * Link a new subject to the user.
     */
    public function linkSubject(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $user = Auth::user();
        
        // Check if the subject is already linked
        if ($user->subjects->contains($request->subject_id)) {
            return redirect()->route('user.dashboard')->with('error', 'Subject is already linked!');
        }

        $user->subjects()->syncWithoutDetaching($request->subject_id);

        return redirect()->route('user.dashboard')->with('success', 'Subject linked successfully!');
    }

    /**
     * Unlink a subject from the user.
     */
    public function unlinkSubject(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $user = Auth::user();
        
        // Check if the subject is currently linked
        if (!$user->subjects->contains($request->subject_id)) {
            return redirect()->route('user.dashboard')->with('error', 'Subject is not linked!');
        }

        $user->subjects()->detach($request->subject_id);

        return redirect()->route('user.dashboard')->with('success', 'Subject unlinked successfully!');
    }

    /**
     * API Fetch the subjects associated with a user.
     */
    public function getUserSubjects($userId)
    {
        // Fetch the user with their subjects
        $user = User::with('subjects')->find($userId);

        // Check if user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Return the user resource
        return new UserResource($user);
    }
}
