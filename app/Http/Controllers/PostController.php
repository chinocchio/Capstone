<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Psy\CodeCleaner\ReturnTypePass;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class PostController extends Controller 
// implements HasMiddleware
{
    // Adding Auth middleware to all methods except 'index' and 'show'
    // public static function middleware(): array
    // {
    //     return [
    //         new Middleware(['admin', 'auth', 'verified'], except: ['index', 'show']),
    //     ];
    // }
    
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        // $posts = Post::latest()->paginate(6);

        // if(Auth::guard('admin')->check())
        // {
        //     return view ('posts.aIndex', [ 'posts' => $posts ]);
        // }
        // return view ('posts.index', [ 'posts' => $posts ]);


        $posts = Post::latest()->paginate(6);

        if(Auth::guard('admin')->check())
        {
            return view ('posts.aIndex', [ 'posts' => $posts ]);
        }
        return view ('posts.index', [ 'posts' => $posts ]);
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
        // Validate
        $request->validate([
            'title' => ['required', 'max:255'],
            'body' => ['required'],
            'image' => ['nullable', 'file', 'max:3000', 'mimes:webp,png,jpg']
        ]);

        // Store image if exists
        $path = null;
        if ($request->hasFile('image')) {
            $path = Storage::disk('public')->put('posts_images', $request->image);
        }

        // Create a post
        Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'image' => $path
        ]);

        //Redirect to dashboard
        return redirect()->route('admin_dashboard')->with('success', 'You added a schedule.'); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        if(Auth::guard('admin')->check())
        {
            return view('posts.aShow', ['post' => $post]);
        }
        return view('posts.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    { 

        // Authorizing the action
        $post = Post::findOrFail($id);
        return view('posts.edit', ['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Authorizing the action
        $post = Post::findOrFail($id);

        // Validate
        $request->validate([
            'title' => ['required', 'max:255'],
            'body' => ['required'],
            'image' => ['nullable', 'file', 'max:3000', 'mimes:webp,png,jpg']
        ]);

        // Store image if exists
        $path = $post->image ?? null;
        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $path = Storage::disk('public')->put('posts_images', $request->image);
        }

        // Update a post
        $post->update([
            'title' => $request->title,
            'body' => $request->body,
            'image' => $path
        ]);

        //Redirect to dashboard
        return redirect()->route('admin_dashboard')->with('success', 'Your schedule was updated.'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Authorizing the action
        // Gate::authorize('modify', $post);
        $post = Post::findOrFail($id);

        // Delete post image if exists
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        // Delete the post
        $post->delete();

        // Redirect back to dashboard
        return back()->with('delete', 'Manual deleted!');
    }

    public function getManuals()
    {
        $posts = Post::all();
        return response()->json($posts, Response::HTTP_OK);
    }
}
