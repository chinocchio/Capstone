<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function getAdminByPin($pin)
    {
        // Find the admin with the given PIN
        $admin = DB::table('admins')
            ->where('pin', $pin)
            ->first();

        if ($admin) {
            return response()->json($admin, 200);
        } else {
            return response()->json(['message' => 'Admin not found or incorrect PIN'], 404);
        }
    }

    public function getAdminByPassword($password)
    {
        // Fetch all admins
        $admins = DB::table('admins')->get();
    
        // Iterate over each admin and check if the password matches
        foreach ($admins as $admin) {
            if (Hash::check($password, $admin->password)) {
                return response()->json($admin, 200);
            }
        }
    
        return response()->json(['message' => 'Admin not found or incorrect password'], 404);
    }

    public function updateAdminByPassword(Request $request, $password)
    {
        // Fetch all admins
        $admins = DB::table('admins')->get();

        // Iterate over each admin and check if the password matches
        foreach ($admins as $admin) {
            if (Hash::check($password, $admin->password)) {
                // Update the admin details
                DB::table('admins')
                    ->where('id', $admin->id)
                    ->update([
                        'pin' => $request->input('pin'),
                        'finger_id' => $request->input('finger_id'),
                        'fingerprint_template' => $request->input('fingerprint_template')
                    ]);

                return response()->json(['message' => 'Admin details updated successfully'], 200);
            }
        }

        return response()->json(['message' => 'Admin not found or incorrect password'], 404);
    }


    public function dashboard()
{
    // Fetch instructors with pagination
    $instructors = User::paginate(6);

    if (Auth::guard('admin')->check()) {
        $now = Carbon::now('Asia/Manila');
        $currentDate = $now->format('Y-m-d'); // Format for SQL comparison
        $currentTime = $now->format('H:i:s'); // Format for SQL comparison
        $today = $now->format('l'); // For day name comparison
        
        // Retrieve subjects with optional instructor information
        $subjects = DB::table('subjects')
            ->leftJoin('user_subject', 'subjects.id', '=', 'user_subject.subject_id')
            ->leftJoin('users', 'user_subject.user_id', '=', 'users.id')
            ->where('subjects.day', $today)
            ->where('subjects.start_time', '<=', $currentTime)
            ->where('subjects.end_time', '>=', $currentTime)
            ->select('subjects.*', 'users.username', 'users.email')
            ->get();

                // Retrieve the latest temperature and humidity data
                $latestTemperature = DB::table('temperature')
                ->latest('created_at')
                ->first();

        return view('admin.admins.dashboard', [
            'instructors' => $instructors,
            'subjects' => $subjects,
            'latestTemperature' => $latestTemperature,
            'currentDate' => $now->format('l, F j, Y') // For display
        ]);
    }
}

    public function userPosts(User $user) {

        $userPosts = $user->posts()->latest()->paginate(6);

        return view ('users.posts', [
            'posts' => $userPosts,
            'user' => $user
        ]);
    }

    public function login()
    {
        return view('admin.auth.login');
    }

    public function login_submit(Request $request)
    {
        // Validate input
        $request->validate([
            'username' => ['required', 'max:255'],
            'password' => ['required'],
        ]);

        // Prepare data for authentication
        $data = [
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ];

        // Attempt to login the user
        if (Auth::guard('admin')->attempt($data)) {
            return redirect()->route('admin_dashboard');
        } else {
            return back()->withErrors([
                'failed' => 'The provided credentials do not match our records.'
            ]);
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/');
    }

    public function edit(User $instructors)
    {
        dd($instructors);
        return view('admin.admins.aEdit', ['instructor' => $instructors]);
    }

    // Method to show the change PIN form for admin
    public function showChangePinForm()
    {
        // Ensure this view path matches your Blade file location
        return view('admin.admins.ChangePinAdmin');
    }

    // Method to handle the change PIN request for admin
    public function changePin(Request $request)
    {
        // Validate the request data
        $request->validate([
            'old_pin' => 'required|digits:4',
            'new_pin' => 'required|digits:4|confirmed',
        ]);

        // Get the currently authenticated admin user
        $admin = Auth::guard('admin')->user();

        // Check if the old PIN matches the current one (plain text comparison)
        if ($request->old_pin != $admin->pin) {
            return back()->withErrors(['old_pin' => 'Old PIN does not match. Please try again.']);
        }

        // Update the admin's PIN
        $admin->pin = $request->new_pin;
        $admin->save();

        return back()->with('success', 'Your PIN has been successfully changed.');
    }
}
