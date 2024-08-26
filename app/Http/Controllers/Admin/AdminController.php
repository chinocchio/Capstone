<?php

namespace App\Http\Controllers\Admin;


use App\Models\User;
use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $instructors = User::paginate(6);

        if(Auth::guard('admin')->check())
        {
            $subjects = DB::table('subjects')
            ->leftJoin('user_subject', 'subjects.id', '=', 'user_subject.subject_id')
            ->leftJoin('users', 'user_subject.user_id', '=', 'users.id')
            ->where('subjects.day', DB::raw('DAYNAME(CURDATE())'))
            ->where('subjects.start_time', '<=', DB::raw('CURTIME()'))
            ->where('subjects.end_time', '>=', DB::raw('CURTIME()'))
            ->select('subjects.*', 'users.username', 'users.email')
            ->get();

            // return view ('admin.admins.dashboard', [ 'instructors' => $instructors ]);

            return view('admin.admins.dashboard', [
                'instructors' => $instructors,
                'subjects' => $subjects,
                'currentDate' => Carbon::now('Asia/Manila')->format('l, F j, Y')
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
}
