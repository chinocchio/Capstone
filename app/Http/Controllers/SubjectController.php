<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubjectImport;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;



class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subject::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('code', 'like', "%{$searchTerm}%");
        }

        $subjects = $query->orderBy('day')->paginate(4);

        return view('admin.admins.addSubject', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view ('admin.admins.createSubject');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input data
        $request->validate([
            'name' => ['required', 'max:255'],
            'code' => ['required'],
            'description' => ['required'],
            'section' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time', // Ensure end_time is after start_time
            'day' => 'required|string',
            'school_year' => 'required|string',
            'semester' => 'required|string',
            'image' => ['nullable', 'image'], // Optional image validation
        ]);
    
        // Convert times to Carbon instances for comparison
        $startTime = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
    
        // Step 1: Check for duplicate sections on the same day, school year, and semester
        $duplicateSections = Subject::where('day', $request->day)
            ->where('section', $request->section)
            ->where('school_year', $request->school_year)
            ->where('semester', $request->semester)
            ->first();
    
        // If duplicate sections exist, prevent the addition
        if ($duplicateSections) {
            return redirect()->back()
                             ->with('duplicate_sections', [$duplicateSections])
                             ->withInput();
        }
    
        // Step 2: Check for overlapping time schedules within the same day, school year, and semester
        $conflictingSubjects = Subject::where('day', $request->day)
            ->where('school_year', $request->school_year)
            ->where('semester', $request->semester)
            ->where(function ($query) use ($startTime, $endTime) {
                // Check if the new subject's time conflicts with existing subjects
                $query->where(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime) // Check if an existing subject's start time is before the new end time
                          ->where('end_time', '>', $startTime); // And the existing subject's end time is after the new start time
                });
            })
            ->get();
    
        // If conflicting subjects exist, prevent the addition
        if ($conflictingSubjects->isNotEmpty()) {
            return redirect()->back()
                             ->with('conflicting_subjects', $conflictingSubjects)
                             ->withInput();
        }
    
        // Store image if exists
        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts_images', 'public');
        }
    
        $generatedCode = mt_rand(11111111111, 99999999999);
    
        // Create the subject
        Subject::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'section' => $request->section,
            'qr' => $generatedCode,
            'start_time' => $request->start_time, // Directly use the 24-hour format from the input
            'end_time' => $request->end_time,     // Directly use the 24-hour format from the input
            'day' => $request->day,
            'school_year' => $request->school_year,
            'semester' => $request->semester,
            'image' => $path,
        ]);
    
        // Redirect to subjects list
        return redirect()->route('subjects.index')->with('success', 'You added a schedule.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the subject by ID
        $subject = Subject::findOrFail($id);
            
        // Perform the delete operation
        $subject->delete();

        // Redirect with a success message
        return redirect()->route('subjects.index')->with('delete', 'Subject deleted successfully.');
    }
    

    public function deleteAll(Request $request)
    {
        $subjectIds = $request->input('subject_ids');
    
        if (!empty($subjectIds)) {
            Subject::whereIn('id', $subjectIds)->delete();
            return redirect()->route('subjects.index')->with('delete', 'Selected subjects have been deleted.');
        }
    
        return redirect()->route('subjects.index')->with('delete', 'No subjects were selected.');
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $subject = Subject::findOrFail($id);
        return view ('admin.admins.editSubject', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate input data with time format
        $request->validate([
            'name' => ['required', 'max:255'],
            'code' => ['required'],
            'description' => ['required'],
            'section' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time', // Ensure end_time is after start_time
            'day' => 'required|string',
            'school_year' => 'required|string',
            'semester' => 'required|string',
            'image' => ['nullable', 'image'], // Optional image validation
        ]);
    
        // Find the subject by ID
        $subject = Subject::findOrFail($id);
    
        // Step 1: Check for duplicate sections on the same day, school year, and semester
        $duplicateSections = Subject::where('day', $request->day)
            ->where('section', $request->section)
            ->where('school_year', $request->school_year)
            ->where('semester', $request->semester)
            ->where('id', '!=', $id) // Exclude the current subject being edited
            ->first();
    
        // If a duplicate section exists, prevent the update
        if ($duplicateSections) {
            return redirect()->back()->with('duplicate_sections', [$duplicateSections])->withInput();
        }
    
        // Step 2: Check if the new subject’s time falls between the start and end time of any other subjects on the same day, school year, and semester
        $conflictingSubjects = Subject::where('day', $request->day)
            ->where('school_year', $request->school_year)
            ->where('semester', $request->semester)
            ->where('id', '!=', $id) // Exclude the current subject being edited
            ->where(function ($query) use ($request) {
                // Check if the new subject's time falls within the time of any existing subjects
                $query->where(function ($query) use ($request) {
                    $query->where('start_time', '<', $request->end_time) // Check if any existing subject's start time is before the new end time
                          ->where('end_time', '>', $request->start_time); // And the existing subject's end time is after the new start time
                });
            })
            ->get();
    
        // If conflicting subjects exist, prevent the update
        if ($conflictingSubjects->isNotEmpty()) {
            return redirect()->back()->with('conflicting_subjects', $conflictingSubjects)->withInput();
        }
    
        // Handle image upload if provided
        if ($request->hasFile('image')) {
            // Store the new image and delete the old one if necessary
            $path = $request->file('image')->store('posts_images', 'public');
            $subject->image = $path;
        }
    
        // Update the subject with validated data
        $subject->update($request->except('image'));
    
        // Redirect back to the subjects index with success message
        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
    }
    
    
    
     
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
            'school_year' => 'required|string',
            'semester' => 'required|string',  // Add validation for semester
        ]);
    
        $schoolYear = $request->input('school_year');
        $semester = $request->input('semester');
    
        // Pass both school_year and semester to the import class
        $import = new SubjectImport($schoolYear, $semester);
        Excel::import($import, $request->file('file'));
    
        $duplicateSubjects = $import->getDuplicateSubjects();
    
        if (!empty($duplicateSubjects)) {
            return redirect()->back()
                             ->with('duplicate_subjects', $duplicateSubjects)
                             ->with('delete', 'Subjects imported with some duplicates. Please review the list.');
        }
    
        return redirect()->back()->with('success', 'Subjects imported successfully!');
    }
    

    public function getScheduleByDay(Request $request, string $day)
    {
        $subjects = Subject::where('day', $day)->get();

        if ($subjects->isEmpty()) {
            return response()->json(['message' => 'No subjects found for this day'], 404);
        }

        return response()->json($subjects);
    }

    public function getScheduleByPinAndDate(Request $request, $pin, $day)
    {
        
    }

    public function selectMakeupClassTime($id)
    {
        $subject = Subject::findOrFail($id);

            // Fetch enrolled students
        $students = DB::table('student_subject')
        ->join('students', 'student_subject.student_id', '=', 'students.id')
        ->where('student_subject.subject_id', $id)
        ->select('students.id', 'students.name', 'students.email')
        ->get();

        // Fetch linked instructors (select username and email from users)
        $instructors = DB::table('user_subject')
            ->join('users', 'user_subject.user_id', '=', 'users.id')
            ->where('user_subject.subject_id', $id)
            ->select('users.id', 'users.username', 'users.email')
            ->get();

        return view('admin.admins.makeup_class_time', compact('subject', 'students', 'instructors'));
    }

    public function storeMakeupClass(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);
    
        // Find the existing subject by ID (this is used as a template for the new entry)
        $existingSubject = Subject::findOrFail($id);
    
        // Convert the specific date to a Carbon instance to get the day of the week
        $specificDate = Carbon::parse($request->date);
        $specificDayOfWeek = $specificDate->format('l'); // Day of the week (e.g., "Monday", "Tuesday")
    
        // Create a new subject record for the makeup class with the updated day of the week
        $newSubject = Subject::create([
            'name' => $existingSubject->name,
            'code' => $existingSubject->code,
            'description' => $existingSubject->description,
            'qr' => $existingSubject->qr,
            'section' => $existingSubject->section,
            'day' => $specificDayOfWeek, // Set this to the day of the week based on the specific_date
            'image' => $existingSubject->image,
            'type' => 'makeup', // Set this as a makeup class
            'school_year' => $existingSubject->school_year,
            'semester' => $existingSubject->semester,
            'specific_date' => $request->date, // New specific date for the makeup class
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);
    
        // Fetch the instructor(s) linked to the existing subject
        $instructors = DB::table('user_subject')
            ->where('subject_id', $existingSubject->id)
            ->pluck('user_id');
    
        // Link the new makeup class to the same instructor(s)
        foreach ($instructors as $userId) {
            DB::table('user_subject')->insert([
                'user_id' => $userId,
                'subject_id' => $newSubject->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        // Fetch the students linked to the existing subject
        $students = DB::table('student_subject')
            ->where('subject_id', $existingSubject->id)
            ->pluck('student_id');
    
        // Link the new makeup class to the same student(s)
        foreach ($students as $studentId) {
            DB::table('student_subject')->insert([
                'student_id' => $studentId,
                'subject_id' => $newSubject->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        // Redirect back with a success message
        return redirect()->route('subjects.index')->with('success', 'New makeup class scheduled for ' . $specificDayOfWeek . ' and linked to instructor(s) and student(s) successfully.');
    }


    public function showCalendar()
    {
        // Fetch all subjects from the database
        $subjects = Subject::all();
        $events = [];

        foreach ($subjects as $subject) {
            // Fetch subject details
            $section = $subject->section;

            // Fetch linked instructors (select username and email from users)
            $instructors = DB::table('user_subject')
            ->join('users', 'user_subject.user_id', '=', 'users.id')
            ->where('user_subject.subject_id', $subject->id)
            ->pluck('users.username'); // Assuming 'username' is the instructor's name

            // Convert the instructors into a string (comma-separated if multiple)
            $instructorNames = $instructors->isEmpty() ? 'No instructor' : implode(', ', $instructors->toArray());

            if ($subject->type === 'makeup') {
                // Makeup class: Use specific date for start and end
                $events[] = [
                    'title' => $subject->name . ' (Makeup Class) - Section: ' . $section . ' - Instructor(s): ' . $instructorNames,
                    'start' => Carbon::parse($subject->specific_date . ' ' . $subject->start_time)->format('Y-m-d\TH:i:s'),
                    'end' => Carbon::parse($subject->specific_date . ' ' . $subject->end_time)->format('Y-m-d\TH:i:s'),
                    'color' => 'red',
                ];
            } else {
                // Regular class: Use day of the week for recurrence
                $dayOfWeek = $subject->day;
                $startTime = $subject->start_time;
                $endTime = $subject->end_time;

                // Add today's event explicitly if the day matches
                if (Carbon::now()->isoFormat('dddd') === $dayOfWeek) {
                    $events[] = [
                        'title' => $subject->name . ' - Section: ' . $section . ' - Instructor(s): ' . $instructorNames,
                        'start' => Carbon::today()->format('Y-m-d') . 'T' . Carbon::parse($startTime)->format('H:i:s'),
                        'end' => Carbon::today()->format('Y-m-d') . 'T' . Carbon::parse($endTime)->format('H:i:s'),
                        'color' => 'blue',
                    ];
                }

                // Recurrence: Repeat for the rest of the days in the month
                $events[] = [
                    'title' => $subject->name . ' - Section: ' . $section . ' - Instructor(s): ' . $instructorNames,
                    'startTime' => Carbon::createFromFormat('H:i:s', $startTime)->format('H:i'),
                    'endTime' => Carbon::createFromFormat('H:i:s', $endTime)->format('H:i'),
                    'daysOfWeek' => [$this->convertDayToNumber($dayOfWeek)], // Convert day name to day number
                    'startRecur' => Carbon::tomorrow()->format('Y-m-d'), // Recurrence starting tomorrow
                    'endRecur' => Carbon::now()->endOfMonth()->format('Y-m-d'), // Recurrence till the end of the month
                    'color' => 'blue',
                ];
            }
        }

        // Pass the events to the calendar view
        return view('admin.admins.calendar', compact('events'));
    }

    // Helper function to convert day names to FullCalendar day numbers
    private function convertDayToNumber($dayOfWeek)
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

        return $days[$dayOfWeek] ?? 0; // Default to Sunday if not found
    }
    
}
