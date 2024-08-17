<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Subject;
use App\Models\User;
use App\Models\Scan;
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

        // $user = Auth::user();
        // $linkedSubjects = $user->subjects->map(function($subject) {
        //     $subject->start_time = Carbon::parse($subject->start_time);
        //     $subject->end_time = Carbon::parse($subject->end_time);
        //     return $subject;
        // });

        // return view('users.attendance', compact('linkedSubjects'));

        // Get the authenticated user


        $user = Auth::user();

        // Fetch the user's subjects and parse the start and end times
        $linkedSubjects = $user->subjects->map(function($subject) {
            $subject->start_time = Carbon::parse($subject->start_time);
            $subject->end_time = Carbon::parse($subject->end_time);
            return $subject;
        });

        // Fetch the scans related to the subjects of the authenticated user
        $scans = Scan::whereIn('subject_id', $linkedSubjects->pluck('id'))
                    ->with('subject')
                    ->orderBy('scanned_at', 'desc')
                    ->get();

        // Pass both the linked subjects and scans to the view
        return view('users.attendance', compact('linkedSubjects', 'scans'));

    }

    public function fetchScans()
    {
        $user = Auth::user();

        // Fetch the user's subjects
        $linkedSubjects = $user->subjects->pluck('id');

        // Fetch the scans related to the user's subjects
        $scans = Scan::whereIn('subject_id', $linkedSubjects)
                    ->with('subject')
                    ->orderBy('scanned_at', 'desc')
                    ->get();

        return view('partials.scans-list', compact('scans'))->render();
    }

    public function toSeatplan(){

        return view('users.seatplan');
    }

    public function toSubjects(){

        return view('users.subjects');
    }
    
}
