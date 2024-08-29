<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class LogsController extends Controller
{
    /**
     * Display the status of the latest log entry.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve the latest log entry
        $latestLog = Logs::latest('created_at')->first();

        // Check if a log entry exists
        if (!$latestLog) {
            return response()->json(['status' => 'No logs found'], Response::HTTP_NOT_FOUND);
        }

        // Return the status of the latest log entry
        return response()->json(['status' => $latestLog->status]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

   /**
     * Store a newly created log in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string|max:255',
            'time' => 'required|date_format:H:i',
            'day' => 'required|string|max:255',
        ]);

        // Create a new log entry
        $log = Logs::create($validated);

        // Return a response indicating success
        return response()->json($log, Response::HTTP_CREATED);
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
