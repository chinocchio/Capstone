<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Subject;
use App\Models\Setting;
use App\Models\User;
use App\Models\StudentAttendance;
use App\Models\Student;
use App\Models\Scan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF; 

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch the current semester and academic year from the settings
        $currentSettings = Setting::first();
        $schoolYear = $currentSettings->academic_year;
        $semester = $currentSettings->current_semester;
    
        // Fetch the posts (user's subjects) for the authenticated user with pagination, filtered by the current semester and school year
        $posts = Auth::user()->subjects()
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->latest()
            ->paginate(6);
    
        // Get the current date and time in the Asia/Manila timezone
        $now = Carbon::now('Asia/Manila');
        $currentDate = $now->format('Y-m-d'); // Format for SQL comparison
        $currentTime = $now->format('H:i:s'); // Format for SQL comparison
        $today = $now->format('l'); // Day of the week (e.g., Monday, Tuesday)
    
        // Retrieve subjects excluding "Pending" and "Vacant" subjects, and filter by current day, time, semester, and school year
        $subjects = DB::table('subjects')
            ->leftJoin('user_subject', 'subjects.id', '=', 'user_subject.subject_id')
            ->leftJoin('users', 'user_subject.user_id', '=', 'users.id')
            ->where('subjects.day', $today) // Ensure the subject is for the current day
            ->where('subjects.school_year', $schoolYear) // Filter by the current school year
            ->where('subjects.semester', $semester) // Filter by the current semester
            ->whereNotIn('subjects.name', ['Vacant', 'Pending']) // Exclude "Vacant" and "Pending" subjects
            ->whereTime('subjects.start_time', '<=', $currentTime) // Ensure the subject's start time is before or equal to the current time
            ->whereTime('subjects.end_time', '>=', $currentTime) // Ensure the subject's end time is after or equal to the current time
            ->select('subjects.*', 'users.username', 'users.email')
            ->get();
    
        // Retrieve the latest temperature and humidity data
        $latestTemperature = DB::table('temperature')
            ->latest('created_at')
            ->first();
    
        // Return the view with the data
        return view('users.dashboard', [
            'posts' => $posts, // User's subjects for the current semester and academic year
            'subjects' => $subjects, // Subjects for the current day and time
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

    // Gawa gawa ko to gumagana 
    // public function toAttendance(Request $request)
    // {
    //     $user = Auth::user();
    //     $now = Carbon::now('Asia/Manila');
    //     $nowTime = $now->format('H:i:s');
    //     $today = $now->format('l'); // Get the current day of the week (e.g., 'Monday')
    
    //     // Fetch the current semester and academic year from the settings
    //     $currentSettings = Setting::first();
    //     $schoolYear = $currentSettings->academic_year;
    //     $semester = $currentSettings->current_semester;
    
    //     // Fetch the user's subjects that are active today and within the current time, filtered by current semester and academic year
    //     $linkedSubjects = $user->subjects()
    //         ->where('school_year', $schoolYear)
    //         ->where('semester', $semester)
    //         ->get()
    //         ->filter(function($subject) use ($nowTime, $today) {
    //             $start_time = Carbon::parse($subject->start_time)->format('H:i:s');
    //             $end_time = Carbon::parse($subject->end_time)->format('H:i:s');
    //             $subjectDay = $subject->day;
    
    //             // Check if current day and time are within the subject's schedule
    //             return $subjectDay === $today && $start_time <= $nowTime && $end_time >= $nowTime;
    //         });
    
    //     // Check if a QR code was provided
    //     $qrCode = $request->input('qr_code');
    //     if ($qrCode) {
    //         // Fetch the subject by QR code, also filter by current semester and academic year
    //         $subject = Subject::where('qr', $qrCode)
    //                           ->where('school_year', $schoolYear)
    //                           ->where('semester', $semester)
    //                           ->first();
    
    //         if ($subject) {
    //             // Fetch the scans related to this subject where fingerprint_verified is true
    //             $scans = Scan::where('subject_id', $subject->id)
    //                 ->where('fingerprint_verified', true)
    //                 ->orderBy('scanned_at', 'desc')
    //                 ->get();
    //         } else {
    //             $scans = collect(); // Return an empty collection if no subject found
    //         }
    //     } else {
    //         // Fetch the scans related to the filtered subjects where fingerprint_verified is true
    //         $scans = Scan::whereIn('subject_id', $linkedSubjects->pluck('id'))
    //             ->where('fingerprint_verified', true)
    //             ->with('subject')
    //             ->orderBy('scanned_at', 'desc')
    //             ->get();
    //     }
    
    //     // Pass both the filtered subjects and scans to the view
    //     return view('users.attendance', [
    //         'linkedSubjects' => $linkedSubjects,
    //         'scans' => $scans,
    //         'currentDate' => $now->format('l, F j, Y') 
    //     ]);
    // }

    public function toAttendance(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Manila');
        $nowTime = $now->format('H:i:s');
        $today = $now->format('l'); // Get the current day of the week (e.g., 'Monday')
    
        // Fetch the current semester and academic year from the settings
        $currentSettings = Setting::first();
        $schoolYear = $currentSettings->academic_year;
        $semester = $currentSettings->current_semester;
    
        // Fetch the user's subjects that are active today and within the current time
        $linkedSubjects = $user->subjects()
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->get()
            ->filter(function ($subject) use ($nowTime, $today) {
                $start_time = Carbon::parse($subject->start_time)->format('H:i:s');
                $end_time = Carbon::parse($subject->end_time)->format('H:i:s');
                $subjectDay = $subject->day;
    
                // Check if current day and time are within the subject's schedule
                return $subjectDay === $today && $start_time <= $nowTime && $end_time >= $nowTime;
            });
    
        // Check if a QR code was provided
        $qrCode = $request->input('qr_code');
        if ($qrCode) {
            // Fetch the subject by QR code
            $subject = Subject::where('qr', $qrCode)
                              ->where('school_year', $schoolYear)
                              ->where('semester', $semester)
                              ->first();
    
            if ($subject) {
                // Fetch the scans related to this subject where fingerprint_verified is true
                $scans = Scan::where('subject_id', $subject->id)
                    ->where('fingerprint_verified', true)
                    ->orderBy('scanned_at', 'desc')
                    ->get();
                    
                // Fetch all students enrolled in this subject
                $students = DB::table('student_subject')
                    ->join('students', 'student_subject.student_id', '=', 'students.id')
                    ->where('student_subject.subject_id', $subject->id)
                    ->select('students.id', 'students.name', 'students.email')
                    ->get();
            } else {
                $scans = collect(); // Return an empty collection if no subject is found
                $students = collect(); // Return an empty collection for students
            }
        } else {
            // Fetch the scans related to the filtered subjects where fingerprint_verified is true
            $scans = Scan::whereIn('subject_id', $linkedSubjects->pluck('id'))
                ->where('fingerprint_verified', true)
                ->with('subject')
                ->orderBy('scanned_at', 'desc')
                ->get();
    
            // Fetch all students enrolled in the linked subjects
            $students = DB::table('student_subject')
                ->join('students', 'student_subject.student_id', '=', 'students.id')
                ->whereIn('student_subject.subject_id', $linkedSubjects->pluck('id'))
                ->select('students.id', 'students.name', 'students.email')
                ->get();
        }
    
        // Pass both the filtered subjects, scans, and students to the view
        return view('users.attendance', [
            'linkedSubjects' => $linkedSubjects,
            'scans' => $scans,
            'students' => $students,
            'currentDate' => $now->format('l, F j, Y') 
        ]);
    }


    public function saveAttendance(Request $request)
    {
        $now = Carbon::now('Asia/Manila');
        $today = $now->format('Y-m-d'); // Current date
    
        // Fetch the current semester and academic year from the settings
        $currentSettings = Setting::first();
        $schoolYear = $currentSettings->academic_year;
        $semester = $currentSettings->current_semester;
    
        // Store the subject_id for later use
        $subjectId = $request->input('subject_id');
    
        // Process each student from the form data
        foreach ($request->input('students', []) as $studentId => $studentData) {
            // Check if the record already exists for the student, subject, and date
            $existingRecord = StudentAttendance::where('student_id', $studentId)
                ->where('subject_id', $subjectId)
                ->where('date', $today)
                ->first();
    
            // If no existing record is found, create a new attendance entry
            if (!$existingRecord) {
                StudentAttendance::create([
                    'student_id' => $studentId,
                    'subject_id' => $subjectId, 
                    'status' => $studentData['status'],
                    'time_in' => $studentData['time_in'],
                    'time_out' => $studentData['time_out'],
                    'date' => $today,
                    'school_year' => $schoolYear,
                    'semester' => $semester,
                ]);
            } 
        }
    
        // After saving the attendance, delete the related scans for the subject
        Scan::where('subject_id', $subjectId)->delete();
    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Attendance saved successfully, and scans cleared.');
    }
    
    
    public function exportAttendance($subjectId)
{
    // Fetch all students enrolled in the subject
    $students = Student::whereHas('subjects', function ($query) use ($subjectId) {
        $query->where('subject_id', $subjectId);
    })->get();

    // Fetch all attendance records for the subject
    $attendances = StudentAttendance::where('subject_id', $subjectId)
        ->orderBy('date', 'asc')
        ->get();

    // Prepare the data for Excel export
    $data = $students->map(function ($student) use ($attendances) {
        // Initialize a row with the student's name
        $row = ['name' => $student->name];

        // Get attendance records for the student, grouped by date
        foreach ($attendances->where('student_id', $student->id) as $attendance) {
            $row[$attendance->date] = $attendance->status;
        }

        return $row;
    });

    // Define headers for the Excel sheet (including date headers)
    $headers = ['Name'];
    $dates = StudentAttendance::where('subject_id', $subjectId)
        ->select('date')
        ->distinct()
        ->orderBy('date', 'asc')
        ->pluck('date')
        ->toArray();

    $headers = array_merge($headers, $dates);

    // Generate Excel file
    return Excel::download(new \App\Exports\AttendanceExport($data, $headers), 'attendance.xlsx');
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
    
    // gumagana
    // public function fetchScans(Request $request)
    // {
    //     $user = Auth::user();
    //     $now = Carbon::now('Asia/Manila');
    //     $nowTime = $now->format('H:i:s');
    //     $today = $now->format('l'); // Get the current day of the week (e.g., 'Monday')
    
    //     // Fetch the current semester and academic year from the settings
    //     $currentSettings = Setting::first();
    //     $schoolYear = $currentSettings->academic_year;
    //     $semester = $currentSettings->current_semester;
    
    //     // Fetch the user's subjects that are active today and within the current time
    //     $linkedSubjects = $user->subjects()
    //         ->where('school_year', $schoolYear)
    //         ->where('semester', $semester)
    //         ->get()
    //         ->filter(function ($subject) use ($nowTime, $today) {
    //             $start_time = Carbon::parse($subject->start_time)->format('H:i:s');
    //             $end_time = Carbon::parse($subject->end_time)->format('H:i:s');
    //             $subjectDay = $subject->day;
    
    //             // Check if current day and time are within the subject's schedule
    //             return $subjectDay === $today && $start_time <= $nowTime && $end_time >= $nowTime;
    //         });
    
    //     // Check if a QR code was provided via GET query parameter
    //     $qrCode = $request->query('qr_code');
    //     if ($qrCode) {
    //         // Fetch the subject by QR code
    //         $subject = Subject::where('qr', $qrCode)
    //             ->where('school_year', $schoolYear)
    //             ->where('semester', $semester)
    //             ->first();
    
    //         if ($subject) {
    //             // Fetch the scans related to this subject
    //             $scans = Scan::where('subject_id', $subject->id)
    //                 ->where('fingerprint_verified', true)
    //                 ->orderBy('scanned_at', 'desc')
    //                 ->with('subject')
    //                 ->get();
    //         } else {
    //             $scans = collect(); // Return an empty collection if no subject is found
    //         }
    //     } else {
    //         // Fetch the scans related to the filtered subjects
    //         $scans = Scan::whereIn('subject_id', $linkedSubjects->pluck('id'))
    //             ->where('fingerprint_verified', true)
    //             ->with('subject')
    //             ->orderBy('scanned_at', 'desc')
    //             ->get();
    //     }
    
    //     // Return the rendered view with the scans
    //     return view('partials.scans-list', compact('scans'))->render();
    // }


public function fetchScans(Request $request)
{
    $user = Auth::user();
    $now = Carbon::now('Asia/Manila');
    $nowTime = $now->format('H:i:s');
    $today = $now->format('l'); // Get the current day of the week (e.g., 'Monday')

    // Fetch the current semester and academic year from the settings
    $currentSettings = Setting::first();
    $schoolYear = $currentSettings->academic_year;
    $semester = $currentSettings->current_semester;

    // Fetch the user's subjects that are active today and within the current time
    $linkedSubjects = $user->subjects()
        ->where('school_year', $schoolYear)
        ->where('semester', $semester)
        ->get()
        ->filter(function ($subject) use ($nowTime, $today) {
            $start_time = Carbon::parse($subject->start_time)->format('H:i:s');
            $end_time = Carbon::parse($subject->end_time)->format('H:i:s');
            $subjectDay = $subject->day;

            // Check if current day and time are within the subject's schedule
            return $subjectDay === $today && $start_time <= $nowTime && $end_time >= $nowTime;
        });

    // Fetch enrolled students for the subjects
    $students = DB::table('student_subject')
        ->join('students', 'student_subject.student_id', '=', 'students.id')
        ->whereIn('student_subject.subject_id', $linkedSubjects->pluck('id'))
        ->select('students.id', 'students.name', 'students.email')
        ->get();

    // Check if a QR code was provided via GET query parameter
    $qrCode = $request->query('qr_code');
    if ($qrCode) {
        // Fetch the subject by QR code
        $subject = Subject::where('qr', $qrCode)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->first();

        if ($subject) {
            // Fetch the scans related to this subject
            $scans = Scan::where('subject_id', $subject->id)
                ->where('fingerprint_verified', true)
                ->orderBy('scanned_at', 'desc')
                ->with('subject')
                ->get();
        } else {
            $scans = collect(); // Return an empty collection if no subject is found
        }
    } else {
        // Fetch the scans related to the filtered subjects
        $scans = Scan::whereIn('subject_id', $linkedSubjects->pluck('id'))
            ->where('fingerprint_verified', true)
            ->with('subject')
            ->orderBy('scanned_at', 'desc')
            ->get();
    }

    // Return the rendered view with both scans and students
    return view('partials.scans-list', compact('scans', 'students'))->render();
}


    

    

    public function toSeatplan()
    {
        // Fetch the current semester and academic year from the settings
        $currentSettings = Setting::first();
        $schoolYear = $currentSettings->academic_year;
        $semester = $currentSettings->current_semester;
    
        // Fetch the user's subjects for the current semester and school year with pagination
        $posts = Auth::user()->subjects()
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->latest()
            ->paginate(6);
    
        // Return the seatplan view with the filtered posts
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
            ->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Student unenrolled successfully.');
    }

    public function toSubjects(){

        return view('users.subjects');
    }

    public function showUserCalendar()
    {
     // Fetch the current semester and academic year from the settings
     $currentSettings = Setting::first();
     $schoolYear = $currentSettings->academic_year;
     $semester = $currentSettings->current_semester;
 
     // Fetch all subjects excluding "vacant" and "pending" and filter by current sem and year
     $subjects = Subject::where('school_year', $schoolYear)
                         ->where('semester', $semester)
                         ->whereNotIn('name', ['Vacant', 'Pending'])
                         ->get();
 
     $events = [];
 
     foreach ($subjects as $subject) {
         // Fetch subject details
         $section = $subject->section;
 
         // Fetch linked instructors (select username from users)
         $instructors = DB::table('user_subject')
                         ->join('users', 'user_subject.user_id', '=', 'users.id')
                         ->where('user_subject.subject_id', $subject->id)
                         ->pluck('users.username'); // Assuming 'username' is the instructor's name
 
         // Convert the instructors into a string (comma-separated if multiple)
         $instructorNames = $instructors->isEmpty() ? 'No instructor' : implode(', ', $instructors->toArray());
 
         // Whether it's a makeup or a regular class, we handle both using the day of the week
         $dayOfWeek = $this->convertDayToNumber($subject->day); // Convert day name to day number
         $startTime = $subject->start_time;
         $endTime = $subject->end_time;
 
         // Set color based on whether the class is a makeup class or regular class
         $color = $subject->type === 'makeup' ? 'red' : 'blue';
 
         // Add the class as a recurring event based on the day of the week
         $events[] = [
             'title' => $subject->name . ($subject->type === 'makeup' ? ' (Makeup Class)' : '') . ' - Section: ' . $section . ' - Instructor(s): ' . $instructorNames,
             'startTime' => Carbon::createFromFormat('H:i:s', $startTime)->format('H:i'),
             'endTime' => Carbon::createFromFormat('H:i:s', $endTime)->format('H:i'),
             'daysOfWeek' => [$dayOfWeek], // Use the correct day of the week for recurrence
             'startRecur' => Carbon::createFromFormat('Y', substr($schoolYear, 0, 4))->startOfYear()->format('Y-m-d'), // Start from beginning of school year
             'endRecur' => Carbon::createFromFormat('Y', substr($schoolYear, 5, 4))->endOfYear()->format('Y-m-d'), // End at the end of the school year
             'color' => $color,
         ];
     }

        return view('users.userCalendar', compact('events', 'schoolYear'));
    }

    private function convertDayToNumber($day)
    {
        $days = [
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
        ];
    
        return $days[$day] ?? null; // Return the day number, or null if not found
    }
    
}
