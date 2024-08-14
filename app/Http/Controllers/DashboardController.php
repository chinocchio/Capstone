<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(){

        $posts = Auth::user()->subjects()->latest()->paginate(6);

        return view('users.dashboard', ['posts' => $posts]);
    }

    public function userPosts(User $user) {

        $userPosts = $user->posts()->latest()->paginate(6);

        if(Auth::guard('admin')->check())
        {
            return view ('users.aPosts', [
                'posts' => $userPosts,
                'user' => $user
            ]);
        }
        return view ('users.posts', [
            'posts' => $userPosts,
            'user' => $user
        ]);
    }

    //Gawa gawa ko to 
    public function toAttendance(){

        $user = Auth::user();
        $linkedSubjects = $user->subjects->map(function($subject) {
            $subject->start_time = Carbon::parse($subject->start_time);
            $subject->end_time = Carbon::parse($subject->end_time);
            return $subject;
        });

        return view('users.attendance', compact('linkedSubjects'));
    }

    public function toSeatplan(){

        return view('users.seatplan');
    }

    public function toSubjects(){

        return view('users.subjects');
    }
    
}
