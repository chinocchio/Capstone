<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiInstructorsController extends Controller
{
    public function index()
    {
        $instructors = User::get();
        if($instructors->count() > 0)
        {
            return UserResource::collection($instructors);
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
