<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subject = Subject::paginate(6);

        return view('admin.admins.addSubject', ['subject' => $subject]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
            'image' => ['nullable', 'image'], // Optional image validation
        ]);

        // Store image if exists
        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts_images', 'public');
        }

        // Create a subject
        Subject::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
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
}
