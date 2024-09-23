<?php

namespace App\Http\Controllers;

use App\Models\Reports;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade;
use PDF; 

class ReportController extends Controller
{
    // Store the report
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'from_email' => 'required|email',
            'to_email' => 'required|email',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,mp4,avi|max:2048'
        ]);

        // Save the file if it exists
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('attachments', 'public');
            $validatedData['attachment_path'] = $filePath;
        }

        // Create a new report
        $report = Reports::create($validatedData);

        // Return a response to the mobile app
        return response()->json(['message' => 'Report submitted successfully', 'report_id' => $report->id], 201);
    }

    // View all reports
    public function index()
    {
        $reports = Reports::all();
        return view('admin.admins.Reports', compact('reports'));
    }

    // Confirm a report
    public function confirm($id)
    {
        $report = Reports::findOrFail($id);
        $report->status = 'Confirmed';
        $report->save();

        // Send back confirmation to the mobile app
        return response()->json(['message' => 'Report confirmed successfully']);
    }

     // Generate and download the PDF
     public function printPdf($id)
     {
         $report = Reports::findOrFail($id);
         $pdf = PDF::loadView('reports.pdf', compact('report'));
         return $pdf->download('incident_report.pdf');
     }

      // View a specific report
    public function show($id)
    {
        // Fetch the report by its ID
        $report = Reports::findOrFail($id);

        // Return the view with the report data
        return view('admin.admins.showReport ', compact('report'));
    }
}

