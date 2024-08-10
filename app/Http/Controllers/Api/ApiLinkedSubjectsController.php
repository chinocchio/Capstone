<?php

namespace App\Http\Controllers\Api;


use App\Http\Resources\LinkedSubjectsResource;
use App\Models\User_Subject;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiLinkedSubjectsController extends Controller
{
    public function index()
    {
        $linkedSubs = User_Subject::get();
        if($linkedSubs->count() > 0)
        {
            return LinkedSubjectsResource::collection($linkedSubs);
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
