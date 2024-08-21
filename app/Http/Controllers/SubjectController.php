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
    public function index()
    {
        $subjects = Subject::paginate(6);

        return view('admin.admins.addSubject', ['subjects' => $subjects]);
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

        
        // Validate
        $request->validate([
            'name' => ['required', 'max:255'],
            'code' => ['required'],
            'description' => ['required'],
            'section' => 'required|string|max:255',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'image' => ['nullable', 'image'], // Optional image validation
        ]);

        // Store image if exists
        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts_images', 'public');
        }

        // Convert start and end times from 12-hour to 24-hour format
        $startTime24 = Carbon::createFromFormat('g:i A', $request->start_time)->format('H:i:s');
        $endTime24 = Carbon::createFromFormat('g:i A', $request->end_time)->format('H:i:s');

        $generatedCode = mt_rand(11111111111,99999999999);

        // Create a subject
        Subject::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'section' => $request->section,
            'qr' => $generatedCode,
            'start_time' => $startTime24,
            'end_time' => $endTime24,
            'image' => $path,
        ]);

        // Redirect to dashboard
        return redirect()->route('subjects.index')->with('success', 'You added a schedule.');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        Excel::import(new SubjectImport, $request->file('file'));

        return redirect()->back()->with('success', 'Subjects imported successfully!');
    }

}
