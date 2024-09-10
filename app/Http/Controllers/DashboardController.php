<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Subject;
use App\Models\User;
use App\Models\Student;
use App\Models\Scan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade;
use PDF; 

class DashboardController extends Controller
{
    public function index()
    {
        $posts = Auth::user()->subjects()->latest()->paginate(6);
    
        $now = Carbon::now('Asia/Manila');
        $currentDate = $now->format('Y-m-d'); // Format for SQL comparison
        $currentTime = $now->format('H:i:s'); // Format for SQL comparison
        $today = $now->format('l'); // For day name comparison
        
        // Retrieve subjects with optional instructor information
        $subjects = DB::table('subjects')
            ->leftJoin('user_subject', 'subjects.id', '=', 'user_subject.subject_id')
            ->leftJoin('users', 'user_subject.user_id', '=', 'users.id')
            ->where('subjects.day', $today) // Ensure the subject is for the current day
            ->whereTime('subjects.start_time', '<=', $currentTime) // Ensure the subject's start time is before or equal to the current time
            ->whereTime('subjects.end_time', '>=', $currentTime) // Ensure the subject's end time is after or equal to the current time
            ->select('subjects.*', 'users.username', 'users.email')
            ->get();
    
        // Retrieve the latest temperature and humidity data
        $latestTemperature = DB::table('temperature')
            ->latest('created_at')
            ->first();
    
        return view('users.dashboard', [
            'posts' => $posts,
            'subjects' => $subjects,
            'latestTemperature' => $latestTemperature,
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
    
        // Fetch the scans related to the filtered subjects and where fingerprint_verified is true
        $scans = Scan::whereIn('subject_id', $linkedSubjects->pluck('id'))
                    ->where('fingerprint_verified', true)
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
    
    public function exportPdf()
    {
        $user = Auth::user();

        $linkedSubjects = $user->subjects->pluck('id');

        $scans = Scan::whereIn('subject_id', $linkedSubjects)
                    ->where('fingerprint_verified', true)
                    ->with('subject')
                    ->orderBy('scanned_at', 'desc')
                    ->get();

        // Start a database transaction
        DB::beginTransaction();

        try {
            $pdf = PDF::loadView('partials.scan-pdf', compact('scans'));

            // Delete all scans after generating the PDF
            Scan::whereIn('subject_id', $linkedSubjects)
                ->where('fingerprint_verified', true)
                ->delete();

            // Commit the transaction
            DB::commit();

            // Return the PDF for download
            return $pdf->download('scan.pdf');
        } catch (\Exception $e) {
            // Rollback the transaction if something went wrong
            DB::rollBack();

            // Handle the error as needed
            return redirect()->back()->with('error', 'Failed to export PDF and delete scans: ' . $e->getMessage());
        }
    }
    

    public function fetchScans()
    {
        $user = Auth::user();
    
        // Fetch the user's subjects
        $linkedSubjects = $user->subjects->pluck('id');
    
        // Fetch the scans related to the user's subjects where fingerprint_verified is true
        $scans = Scan::whereIn('subject_id', $linkedSubjects)
                    ->where('fingerprint_verified', true) // Only fetch scans where fingerprint_verified is true
                    ->with('subject')
                    ->orderBy('scanned_at', 'desc')
                    ->get();
    
        return view('partials.scans-list', compact('scans'))->render();
    }
    

    public function toSeatplan()
    {
        $posts = Auth::user()->subjects()->latest()->paginate(6);

        return view('users.seatplan', compact('posts'));
    }

    public function importStudents(Request $request)
    {
        // Get the subjects with the same section as the students
        $section = $request->input('section');

        $subjects = DB::table('subjects')
        ->where('subjects.section', $section) // Ensure the subject is for the current day
        ->get();

        $students = DB::table('students')
        ->where('students.section', $section) // Ensure the subject is for the current day
        ->get();

        // Check if there are any students for the given section
        if ($students->isEmpty()) {
            return redirect()->back()->with('warning', 'No students found for the specified section.');
        }

        // Check if there are subjects for the given section
        if ($subjects->isEmpty()) {
            return redirect()->back()->with('warning', 'No subjects found for the specified section.');
        }

        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                // Insert into student_subject table
                \DB::table('student_subject')->updateOrInsert(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id
                    ],
                    []
                );
            }
        }

        return redirect()->back()->with('success', 'Students and subjects imported successfully.');
    }

    public function checkStudents(Request $request)
    {
        $id = $request->input('subject_id');

        // Fetch students enrolled in the given subject
        $students = \DB::table('student_subject')
        ->join('students', 'student_subject.student_id', '=', 'students.id')
        ->where('student_subject.subject_id', $id)
        ->select('students.id','students.student_number', 'students.name', 'students.email', 'students.biometric_data') // Adjust fields as necessary
        ->get();

        return view('users.enrolledStudentList', compact('students'));
    }

    public function unenroll($id)
    {
        // Find the student_subject entry and delete it
        \DB::table('student_subject')
            ->where('student_id', $id)
            ->where('subject_id', request()->input('subject_id')) // Ensure to pass subject_id in your request
            ->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Student unenrolled successfully.');
    }

    public function toSubjects(){

        return view('users.subjects');
    }

    public function showUserCalendar()
    {
        $subjects = Subject::all();
        $events = [];
    
        foreach ($subjects as $subject) {
            // Fetch the section of the subject
            $section = $subject->section;
    
            // Fetch linked instructors (select username and email from users)
            $instructors = DB::table('user_subject')
                ->join('users', 'user_subject.user_id', '=', 'users.id')
                ->where('user_subject.subject_id', $subject->id)
                ->pluck('users.username'); // Assuming 'username' is the instructor's name
    
            // Convert the instructors into a string (comma-separated if multiple)
            $instructorNames = $instructors->isEmpty() ? 'No instructor' : implode(', ', $instructors->toArray());
    
            if ($subject->type === 'makeup') {
                // If it's a makeup class, use the specific_date for the start
                $events[] = [
                    'title' => $subject->name . ' (Makeup Class) - Section: ' . $section . ' - Instructor(s): ' . $instructorNames,
                    'start' => Carbon::parse($subject->specific_date . ' ' . $subject->start_time)->format('Y-m-d\TH:i:s'),
                    'end' => Carbon::parse($subject->specific_date . ' ' . $subject->end_time)->format('Y-m-d\TH:i:s'),
                    'color' => 'red',
                ];
            } else {
                // If it's a regular subject, use the day of the week and combine it with the time
                $dayOfWeek = $subject->day;
                $startTime = $subject->start_time;
                $endTime = $subject->end_time;
    
                // Find the next occurrence of the day in this week
                $startDateTime = Carbon::now()->next($dayOfWeek)->setTimeFromTimeString($startTime)->format('Y-m-d\TH:i:s');
                $endDateTime = Carbon::now()->next($dayOfWeek)->setTimeFromTimeString($endTime)->format('Y-m-d\TH:i:s');
    
                $events[] = [
                    'title' => $subject->name . ' - Section: ' . $section . ' - Instructor(s): ' . $instructorNames,
                    'start' => $startDateTime,
                    'end' => $endDateTime,
                    'color' => 'blue',
                ];
            }
        }

        return view('users.userCalendar', compact('events'));
    }
    
}
