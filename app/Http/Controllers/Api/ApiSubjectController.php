<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiSubjectController extends Controller
{
    public function index()
    {
        // Fetch the current semester and academic year from the settings
        $settings = Setting::first();
        $currentSemester = $settings->current_semester ?? 'Unknown Semester';
        $currentSchoolYear = $settings->academic_year ?? 'Unknown School Year';
    
        // Fetch subjects filtered by current school year and semester
        $subjects = Subject::where('school_year', $currentSchoolYear)
                           ->where('semester', $currentSemester)
                           ->get();
    
        // Return subjects if available, otherwise return no subjects message
        if($subjects->count() > 0)
        {
            return SubjectResource::collection($subjects);
        }
        else
        {
            return response()->json(['message' => 'No subjects recorded for the current semester and school year'], 200);
        }
    }
    
    public function store()
    {
        
    }
    public function show()
    {
        
    }
    public function update()
    {
        
    }
    public function destroy()
    {
        
    }
    public function getScheduleByDay(Request $request, $day)
    {
        dd($request);
        $subjects = Subject::where('day', $day)->get();
    
        if ($subjects->count() > 0) {
            return SubjectResource::collection($subjects);
        }
    
        return response()->json(['message' => 'No subjects Recorded'], 200);
    }
}
