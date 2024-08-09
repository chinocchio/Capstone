<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiSubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::get();
        if($subjects->count() > 0)
        {
            return SubjectResource::collection($subjects);
        }
        else
        {
            return response()->json(['message' => 'No subjects Recorde'], 200);
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
}
