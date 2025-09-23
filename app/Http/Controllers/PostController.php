<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with("user")->latest()->paginate(10);
//        User::find($post->user_id);

        return view("home", compact("posts"));
//        return view("posts.index", compact("posts"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("posts.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|max:255",
            "content" => "required"
        ]);
        auth()->user()->posts()->create($validated);
        return redirect()->route("posts.index")
            ->with("success", "Статья успешно создана!");
    }
//

    /**
     * Display the specified resource.
     */
    public function show(int $id)//Post $post)
    {
        $post = Post::findOrFail($id);//->load("comments.user");
        return view("posts.show", compact("post"));
//        return view("posts.show", compact("post"));
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
