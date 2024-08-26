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
    public function destroy(Request $request)
    {
    
    }

    public function delete(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id'
        ]);

        // Find the record to delete
        $deleted = User_Subject::where('user_id', $request->user_id)
                               ->where('subject_id', $request->subject_id)
                               ->delete();

        // Check if any records were deleted
        if ($deleted) {
            return response()->json([
                'message' => 'Linked Subject removed successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Record not found'
            ], 404);
        }
    }
}
