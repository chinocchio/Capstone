<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Psy\CodeCleaner\ReturnTypePass;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instructor = User::all();
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
        //
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
    public function edit($instructor)
    {   
        $instructor = User::find($instructor);

        return view('admin.admins.aEdit', ['instructor' => $instructor]);    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $instructor)
    {

        $instructor = User::find($instructor);
        $instructor->update($request->all());
        return redirect()->route('admin_dashboard');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::destroy($id);
        return redirect()->route('admin_dashboard');
    }
}
