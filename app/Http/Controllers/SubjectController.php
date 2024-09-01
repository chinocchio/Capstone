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

        $subjects = $query->paginate(6);

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
            'end_time' => 'required|date_format:H:i',
            'day' => 'required|string',
            'image' => ['nullable', 'image'], // Optional image validation
        ]);
    
        // Store image if exists
        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts_images', 'public');
        }
    
        $generatedCode = mt_rand(11111111111, 99999999999);
    
        // Create a subject
        Subject::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'section' => $request->section,
            'qr' => $generatedCode,
            'start_time' => $request->start_time, // Directly use the 24-hour format from the input
            'end_time' => $request->end_time,     // Directly use the 24-hour format from the input
            'day' => $request->day,
            'image' => $path,
        ]);
    
        // Redirect to dashboard
        return redirect()->route('subjects.index')->with('success', 'You added a schedule.');
    }

    public function deleteAll(Request $request)
    {
        dd($request);
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
        $subject = Subject::findOrFail($id);
        $subject->update($request->all());
        return redirect()->route('subjects.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Subject::destroy($id);
        return redirect()->route('subjects.index');
    }
    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        Excel::import(new SubjectImport, $request->file('file'));

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
