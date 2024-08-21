<?php

namespace App\Http\Controllers\Api;


use App\Http\Resources\LinkedSubjectsResource;
use App\Models\User;
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
    public function store(Request $request)
    {

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id'
        ]);

        $data = User_Subject::create([
            'user_id' => $request->user_id,
            'subject_id' => $request->subject_id
        ]);

        return response()->json([
            'message' => 'Linked Subjects added Successfully',
            'data' => new LinkedSubjectsResource($data)
        ]);
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
