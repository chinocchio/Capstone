<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Setting;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InstructorsImport;
use Illuminate\Support\Facades\Validator;


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
        return view('admin.admins.createInstructors');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the form input
        $validator = Validator::make($request->all(), [
            'instructor_number' => 'required|unique:users,instructor_number|max:255',
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'school_year' => 'required',
            'semester' => 'required',
            'image' => 'nullable|image|max:2048', // Optional image field
        ]);

        // If validation fails, return with errors
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle file upload if an image is uploaded
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
        }

        // Create and save the user
        User::create([
            'instructor_number' => $request->instructor_number,
            'username' => $request->name,
            'email' => $request->email,
            'school_year' => $request->school_year,
            'semester' => $request->semester,
            'image' => $request->hasFile('image') ? $imagePath : null,
        ]);

        // Redirect with a success message
        return redirect()->route('users.show')->with('success', 'User added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
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

        // Validate the request
        $validated = $request->validate([
            'instructor_number' => 'nullable|string|unique:users,instructor_number,' . $instructorId,
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $instructorId,
            'pin' => 'nullable|integer',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'school_year' => 'nullable|string',
            'semester' => 'nullable|string',
        ]);
    
        // Check if avatar is uploaded
        if ($request->hasFile('avatar')) {
            $avatarName = time().'.'.$request->avatar->extension();  
            $request->avatar->move(public_path('avatars'), $avatarName);
            $validated['avatar'] = $avatarName;
        }
    
        // Update the instructor data
        $instructor->update($validated);
    
        // Redirect back
        return redirect()->route('admin_dashboard')->with('success', 'Instructor updated successfully');
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
    
        // Fetch the current semester and academic year from the settings
        $currentSettings = Setting::first();
        $schoolYear = $currentSettings->academic_year;
        $semester = $currentSettings->current_semester;
        
        // Subjects currently linked to the user, filtered by current semester and academic year
        $linkedSubjects = $user->subjects()
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->get()
            ->map(function ($subject) {
                $subject->start_time = Carbon::parse($subject->start_time);
                $subject->end_time = Carbon::parse($subject->end_time);
                return $subject;
            });
    
        // All subjects that are not linked to any user and have 'type' as null (regular classes),
        // filtered by current semester and academic year, and excluding "Pending" and "Vacant"
        $availableSubjects = Subject::whereDoesntHave('users')
            ->whereNull('type') // Filter to include only subjects where 'type' is null
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->whereNotIn('name', ['Vacant', 'Pending']) // Exclude "Vacant" and "Pending" subjects
            ->get()
            ->map(function ($subject) {
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        // Get the current school year and semester from settings
        $currentSettings = Setting::first();
        $schoolYear = $currentSettings->academic_year;
        $semester = $currentSettings->current_semester;

        // Create a new instance of the InstructorsImport class with the current school year and semester
        $import = new InstructorsImport($schoolYear, $semester);

        // Import the file
        Excel::import($import, $request->file('file'));

        // Return success or handle duplicates as needed
        return redirect()->back()->with('success', 'Instructors imported successfully.');
    }

    public function userShow(Request $request)
    {
        // Get query parameters for search, school_year, and semester
        $search = $request->input('search');
        $schoolYear = $request->input('school_year');
        $semester = $request->input('semester');

        // Query to fetch users with optional search and filters
        $query = User::query();

        if ($search) {
            $query->where('username', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('instructor_number', 'like', "%{$search}%");
        }

        if ($schoolYear) {
            $query->where('school_year', $schoolYear);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        $users = $query->paginate(10); // Adjust pagination as needed

        return view('admin.admins.addInstructors', compact('users', 'search', 'schoolYear', 'semester'));
    }

    // For bulk deletion
    public function deleteSelected(Request $request)
    {
        $userIds = $request->input('selected_users');
    
        if ($userIds) {
            // Delete the selected users
            User::whereIn('id', $userIds)->delete();
    
            return redirect()->back()->with('success', 'Selected users deleted successfully.');
        }
    
        return redirect()->back()->with('error', 'No users selected for deletion.');
    }

    public function getAllInstructorsWithSubjects()
    {
        // Fetch all instructors with their linked subjects
        $instructors = User::with(['subjects' => function ($query) {
            // Optional: Select specific fields if needed
            $query->select('id', 'name', 'code', 'description', 'start_time', 'end_time', 'section', 'day', 'type', 'school_year', 'semester');
        }])->get(['id', 'instructor_number', 'username', 'email', 'school_year', 'semester']);

        return response()->json($instructors);
    }

    // Method to change PIN using PUT
    public function changePin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'old_pin' => 'required|integer',
            'new_pin' => 'required|integer|digits:4|unique:users,pin',
        ]);

        $user = User::find($request->user_id);

        // Check if the old PIN matches
        if ($user->pin !== $request->old_pin) {
            return response()->json(['message' => 'Old PIN does not match. Please try again.'], 400);
        }

        // Update with the new PIN
        $user->pin = $request->new_pin;
        $user->save();

        return response()->json(['message' => 'PIN updated successfully.']);
    }

    // Method to get the old PIN
    public function getOldPin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Return the current PIN securely
        return response()->json(['pin' => $user->pin]);
    }

    public function changePinUser(Request $request)
    {
        // Validate the request data
        $request->validate([
            'old_pin' => 'required|digits:4',
            'new_pin' => 'required|digits:4|confirmed',
        ]);

        // Get the currently authenticated user
        $user = Auth::user();

        // Check if the old PIN matches the current one (plain text comparison)
        if ($request->old_pin != $user->pin) {
            return back()->withErrors(['old_pin' => 'Old PIN does not match. Please try again.']);
        }

        // Update the user's PIN
        $user->pin = $request->new_pin;
        $user->save();

        return back()->with('success', 'Your PIN has been successfully changed.');
    }

    public function showChangePinForm()
    {
        return view('users.ChangePin'); // Make sure this matches your actual view file path
    }
}
