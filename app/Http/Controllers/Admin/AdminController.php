<?php

namespace App\Http\Controllers\Admin;


use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class AdminController extends Controller
{
    public function dashboard()
    {
        $instructors = User::paginate(6);

        if(Auth::guard('admin')->check())
        {
            return view ('admin.admins.dashboard', [ 'instructors' => $instructors ]);
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
}
