<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubjectImport;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Setting;
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
        // Fetch the current semester and academic year from settings
        $currentSettings = Setting::first();
        $currentSemester = $currentSettings->current_semester;
        $currentYear = $currentSettings->academic_year;

        // Initialize the selected day variable
        $selectedDay = $request->input('day', '');  // Default to empty string if no day is selected

        // Query subjects based on the current semester and academic year
        $query = Subject::where('school_year', $currentYear)
                        ->where('semester', $currentSemester);

        // Apply search filters if present
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('code', 'like', "%{$searchTerm}%");
            });
        }

        // Apply day filter if present
        if ($selectedDay !== '') {
            $query->where('day', $selectedDay);
        }

        // Paginate the results (10 per page)
        $subjects = $query->orderBy('day')->paginate(10);

        // Pass the selected day, current semester, and current year to the view
        return view('admin.admins.addSubject', compact('subjects', 'currentSemester', 'currentYear', 'selectedDay'));
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
            'image' => ['nullable', 'image'], // Optional image validation
        ]);
    
        // Fetch the current semester and academic year from the settings
        $currentSettings = Setting::first();
        $schoolYear = $currentSettings->academic_year ?? 'Default Year';
        $semester = $currentSettings->current_semester ?? 'Default Semester';
    
        // Convert times to Carbon instances for comparison
        $startTime = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
    
        // Step 1: Check for duplicate sections on the same day, school year, and semester
        $duplicateSections = Subject::where('day', $request->day)
            ->where('section', $request->section)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->first();
    
        // If duplicate sections exist, prevent the addition
        if ($duplicateSections) {
            return redirect()->back()
                             ->with('duplicate_sections', [$duplicateSections])
                             ->withInput();
        }
    
        // Step 2: Check for overlapping time schedules within the same day, school year, and semester
        $conflictingSubjects = Subject::where('day', $request->day)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
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
            'school_year' => $schoolYear, // Use the dynamically fetched school year
            'semester' => $semester,      // Use the dynamically fetched semester
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
        // Validate the file upload
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);
    
        // Fetch the current school year and semester from the settings
        $currentSettings = Setting::first();
        $schoolYear = $currentSettings->academic_year;
        $semester = $currentSettings->current_semester;
    
        // Pass the current school_year and semester to the SubjectImport class
        $import = new SubjectImport($schoolYear, $semester);
    
        // Import the file using Excel::import
        Excel::import($import, $request->file('file'));
    
        // Get the duplicate subjects from the import class
        $duplicateSubjects = $import->getDuplicateSubjects();
    
        // Check if there are duplicates and return a specific message
        if (!empty($duplicateSubjects)) {
            return redirect()->back()
                             ->with('duplicate_subjects', $duplicateSubjects)
                             ->with('delete', 'Subjects imported with some duplicates. Please review the list.');
        }
    
        // If everything is successful, return a success message
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

        // Fetch all available slots that are marked as "Pending" or "Vacant"
        $availableSlots = Subject::whereIn('name', ['Pending', 'Vacant'])
        ->where('school_year', $subject->school_year)
        ->where('semester', $subject->semester)
        ->select('id', 'day', 'start_time', 'end_time')
        ->get();

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

        return view('admin.admins.makeup_class_time', compact('subject', 'students', 'instructors', 'availableSlots'));
    }

    // public function storeMakeupClass(Request $request, $id)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'date' => 'required|date',
    //         'start_time' => 'required',
    //         'end_time' => 'required|after:start_time',
    //     ]);
    
    //     // Find the existing subject by ID (this is used as a template for the new entry)
    //     $existingSubject = Subject::findOrFail($id);
    
    //     // Convert the specific date to a Carbon instance to get the day of the week
    //     $specificDate = Carbon::parse($request->date);
    //     $specificDayOfWeek = $specificDate->format('l'); // Day of the week (e.g., "Monday", "Tuesday")
    
    //     // Create a new subject record for the makeup class with the updated day of the week
    //     $newSubject = Subject::create([
    //         'name' => $existingSubject->name,
    //         'code' => $existingSubject->code,
    //         'description' => $existingSubject->description,
    //         'qr' => $existingSubject->qr,
    //         'section' => $existingSubject->section,
    //         'day' => $specificDayOfWeek, // Set this to the day of the week based on the specific_date
    //         'image' => $existingSubject->image,
    //         'type' => 'makeup', // Set this as a makeup class
    //         'school_year' => $existingSubject->school_year,
    //         'semester' => $existingSubject->semester,
    //         'specific_date' => $request->date, // New specific date for the makeup class
    //         'start_time' => $request->start_time,
    //         'end_time' => $request->end_time,
    //     ]);
    
    //     // Fetch the instructor(s) linked to the existing subject
    //     $instructors = DB::table('user_subject')
    //         ->where('subject_id', $existingSubject->id)
    //         ->pluck('user_id');
    
    //     // Link the new makeup class to the same instructor(s)
    //     foreach ($instructors as $userId) {
    //         DB::table('user_subject')->insert([
    //             'user_id' => $userId,
    //             'subject_id' => $newSubject->id,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }
    
    //     // Fetch the students linked to the existing subject
    //     $students = DB::table('student_subject')
    //         ->where('subject_id', $existingSubject->id)
    //         ->pluck('student_id');
    
    //     // Link the new makeup class to the same student(s)
    //     foreach ($students as $studentId) {
    //         DB::table('student_subject')->insert([
    //             'student_id' => $studentId,
    //             'subject_id' => $newSubject->id,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }
    
    //     // Redirect back with a success message
    //     return redirect()->route('subjects.index')->with('success', 'New makeup class scheduled for ' . $specificDayOfWeek . ' and linked to instructor(s) and student(s) successfully.');
    // }

    public function storeMakeupClass(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'slot' => 'required|exists:subjects,id',
        ]);
    
        // Fetch the selected slot (Pending/Vacant)
        $selectedSlot = Subject::findOrFail($request->slot);
    
        // Find the existing subject by ID (this is used as a template for the new entry)
        $existingSubject = Subject::findOrFail($id);
    
        // Create a new subject record for the makeup class with the selected slot's schedule
        $newSubject = Subject::create([
            'name' => $existingSubject->name,
            'code' => $existingSubject->code,
            'description' => $existingSubject->description,
            'qr' => $existingSubject->qr,
            'section' => $existingSubject->section,
            'day' => $selectedSlot->day, // Use the day from the selected slot
            'image' => $existingSubject->image,
            'type' => 'makeup', // Mark it as a makeup class
            'school_year' => $existingSubject->school_year,
            'semester' => $existingSubject->semester,
            'specific_date' => $selectedSlot->specific_date, // New specific date for the makeup class
            'start_time' => $selectedSlot->start_time,
            'end_time' => $selectedSlot->end_time,
        ]);
    
        // Sync users and students to the new makeup subject (from the original subject)
        $newSubject->users()->sync($existingSubject->users->pluck('id')->toArray());
        $newSubject->students()->sync($existingSubject->students->pluck('id')->toArray());
    
        return redirect()->route('subjects.index')->with('success', 'New makeup class scheduled successfully using the selected vacant/pending slot.');
    }
    
    
    

    public function showCalendar()
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
    
        // Pass the events and the start year to the calendar view
        return view('admin.admins.calendar', compact('events', 'schoolYear'));
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
    
    // public function showCalendar()
    // {
    //     // Fetch all subjects from the database
    //     $subjects = Subject::all();
    //     $events = [];

    //     foreach ($subjects as $subject) {
    //         // Fetch subject details
    //         $section = $subject->section;

    //         // Fetch linked instructors (select username and email from users)
    //         $instructors = DB::table('user_subject')
    //         ->join('users', 'user_subject.user_id', '=', 'users.id')
    //         ->where('user_subject.subject_id', $subject->id)
    //         ->pluck('users.username'); // Assuming 'username' is the instructor's name

    //         // Convert the instructors into a string (comma-separated if multiple)
    //         $instructorNames = $instructors->isEmpty() ? 'No instructor' : implode(', ', $instructors->toArray());

    //         if ($subject->type === 'makeup') {
    //             // Makeup class: Use specific date for start and end
    //             $events[] = [
    //                 'title' => $subject->name . ' (Makeup Class) - Section: ' . $section . ' - Instructor(s): ' . $instructorNames,
    //                 'start' => Carbon::parse($subject->specific_date . ' ' . $subject->start_time)->format('Y-m-d\TH:i:s'),
    //                 'end' => Carbon::parse($subject->specific_date . ' ' . $subject->end_time)->format('Y-m-d\TH:i:s'),
    //                 'color' => 'red',
    //             ];
    //         } else {
    //             // Regular class: Use day of the week for recurrence
    //             $dayOfWeek = $subject->day;
    //             $startTime = $subject->start_time;
    //             $endTime = $subject->end_time;

    //             // Add today's event explicitly if the day matches
    //             if (Carbon::now()->isoFormat('dddd') === $dayOfWeek) {
    //                 $events[] = [
    //                     'title' => $subject->name . ' - Section: ' . $section . ' - Instructor(s): ' . $instructorNames,
    //                     'start' => Carbon::today()->format('Y-m-d') . 'T' . Carbon::parse($startTime)->format('H:i:s'),
    //                     'end' => Carbon::today()->format('Y-m-d') . 'T' . Carbon::parse($endTime)->format('H:i:s'),
    //                     'color' => 'blue',
    //                 ];
    //             }

    //             // Recurrence: Repeat for the rest of the days in the month
    //             $events[] = [
    //                 'title' => $subject->name . ' - Section: ' . $section . ' - Instructor(s): ' . $instructorNames,
    //                 'startTime' => Carbon::createFromFormat('H:i:s', $startTime)->format('H:i'),
    //                 'endTime' => Carbon::createFromFormat('H:i:s', $endTime)->format('H:i'),
    //                 'daysOfWeek' => [$this->convertDayToNumber($dayOfWeek)], // Convert day name to day number
    //                 'startRecur' => Carbon::tomorrow()->format('Y-m-d'), // Recurrence starting tomorrow
    //                 'endRecur' => Carbon::now()->endOfMonth()->format('Y-m-d'), // Recurrence till the end of the month
    //                 'color' => 'blue',
    //             ];
    //         }
    //     }

    //     // Pass the events to the calendar view
    //     return view('admin.admins.calendar', compact('events'));
    // }

    // // Helper function to convert day names to FullCalendar day numbers
    // private function convertDayToNumber($dayOfWeek)
    // {
    //     $days = [
    //         'Sunday' => 0,
    //         'Monday' => 1,
    //         'Tuesday' => 2,
    //         'Wednesday' => 3,
    //         'Thursday' => 4,
    //         'Friday' => 5,
    //         'Saturday' => 6,
    //     ];

    //     return $days[$dayOfWeek] ?? 0; // Default to Sunday if not found
    // }

    public function getAllSubjects()
    {
        // Fetch the current semester and academic year from the settings
        $currentSettings = Setting::first();
        $schoolYear = $currentSettings->academic_year;
        $semester = $currentSettings->current_semester;
    
        // Fetch subjects filtered by the current school year and semester
        $subjects = Subject::with(['students' => function ($query) {
            // Select only the required fields from students, specifying the table
            $query->select('students.id', 'students.name', 'students.student_number', 'students.email');
        }])
            ->where('school_year', $schoolYear) // Filter by current school year
            ->where('semester', $semester) // Filter by current semester
            ->select(
                'subjects.id', 
                'subjects.name', 
                'subjects.code', 
                'subjects.description', 
                'subjects.start_time', 
                'subjects.end_time', 
                'subjects.section', 
                'subjects.day', 
                'subjects.image', 
                'subjects.type', 
                'subjects.specific_date', 
                'subjects.school_year', 
                'subjects.semester'
            )
            ->get()
            ->map(function ($subject) {
                // Modify the type field based on its value
                $subject->type = $subject->type ?? 'regular';
                return $subject;
            });
    
        return response()->json($subjects);
    }
    
    
}
