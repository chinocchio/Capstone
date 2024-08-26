<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Subject;
use App\Models\User;
use App\Models\Scan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(){

        $posts = Auth::user()->subjects()->latest()->paginate(6);
        // Get the current date and time in 'Asia/Manila' time zone
        $now = Carbon::now('Asia/Manila');
        $currentDate = $now->format('Y-m-d'); // Format for SQL comparison
        $currentTime = $now->format('H:i:s'); // Format for SQL comparison

        // Retrieve posts with pagination
        $posts = Auth::user()->subjects()->latest()->paginate(6);

        // Get the current subjects using query builder
        $subjects = DB::table('subjects')
            ->leftJoin('user_subject', 'subjects.id', '=', 'user_subject.subject_id')
            ->leftJoin('users', 'user_subject.user_id', '=', 'users.id')
            ->where(DB::raw('DAYNAME(CONVERT_TZ(subjects.day, @@session.time_zone, \'+08:00\'))'), '=', DB::raw('DAYNAME(\'' . $currentDate . '\')'))
            ->where(DB::raw('CONVERT_TZ(subjects.start_time, @@session.time_zone, \'+08:00\')'), '<=', $currentTime)
            ->where(DB::raw('CONVERT_TZ(subjects.end_time, @@session.time_zone, \'+08:00\')'), '>=', $currentTime)
            ->select('subjects.*', 'users.username', 'users.email')
            ->get();

        return view('users.dashboard', [
            'posts' => $posts,
            'subjects' => $subjects,
            'currentDate' => $now->format('l, F j, Y') // Format for display
        ]);
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
    public function toAttendance()
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Manila');
        $nowTime = $now->format('H:i:s');
        $today = $now->format('l'); // Get the current day of the week (e.g., 'Monday')

        
        // Fetch the user's subjects that are active today and within the current time
        $linkedSubjects = $user->subjects->filter(function($subject) use ($nowTime, $today) {
            $start_time = Carbon::parse($subject->start_time)->format('H:i:s');
            $end_time = Carbon::parse($subject->end_time)->format('H:i:s');
            $subjectDay = $subject->day;
    
            // Check if current day and time are within the subject's schedule
            return $subjectDay === $today && $start_time <= $nowTime && $end_time >= $nowTime;
        });
    
        // Fetch the scans related to the filtered subjects
        $scans = Scan::whereIn('subject_id', $linkedSubjects->pluck('id'))
                    ->with('subject')
                    ->orderBy('scanned_at', 'desc')
                    ->get();
    
        // Pass both the filtered subjects and scans to the view
        return view('users.attendance', [
            'linkedSubjects' => $linkedSubjects,
            'scans' => $scans,
            'currentDate' => $now->format('l, F j, Y')
        ]);
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
