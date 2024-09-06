<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubjectImport;
use Illuminate\Http\Request;
use App\Models\Subject;
use Carbon\Carbon;


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
}
