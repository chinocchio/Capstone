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
        return view ("admin.admins.addMacs", compact('macs'));
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
        dd('edit mac this id = ' . $id);
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
        dd('delete mac');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        Excel::import(new MacImport, $request->file('file'));

        return redirect()->back()->with('success', 'MAC Computers imported successfully!');
    }
}
