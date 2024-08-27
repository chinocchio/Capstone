<?php

namespace App\Http\Controllers;


use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MacImport;
use Illuminate\Http\Request;
use App\Models\Mac;

class MacController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Mac::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('mac_number', 'like', "%{$searchTerm}%")
                ->orWhere('id', 'like', "%{$searchTerm}%");
        }
    
        $macs = $query->paginate(5);
    
        // Fetch linked students for each Mac record
        $linkedStudents = [];
        foreach ($macs as $mac) {
            $linkedStudents[$mac->id] = $mac->students;
        }
    
        return view("admin.admins.addMacs", compact('macs', 'linkedStudents'));
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
        $mac = Mac::findOrFail($id);

        return view ('admin.admins.editMacs', compact('mac'));
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
        $mac = Mac::find($id);

        if (!$mac) {
            return response()->json(['error' => 'MAC not found'], 404);
        }

        // Detach all linked students
        $mac->students()->detach();

        // Delete the MAC address
        $mac->delete();

        return redirect()->back()->with('success', 'MAC Computer deleted successfully!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        Excel::import(new MacImport, $request->file('file'));

        return redirect()->back()->with('success', 'MAC Computers imported successfully!');
    }

    //API FOR MAC LINKING 
    public function getStudents($id)
    {
        $mac = Mac::find($id); // Using find() to search by ID

        if (!$mac) {
            return response()->json(['error' => 'MAC not found'], 404);
        }

        $students = $mac->students; // Assuming a many-to-many relationship
        return response()->json($students);
    }
}
