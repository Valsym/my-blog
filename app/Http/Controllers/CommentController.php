<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Отображение комментариев для поста
     */
    public function index(Post $post)
    {
        $comments = $post->comments()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Сохранение нового комментария
     */
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'body' => 'required|min:3|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = new Comment();
        $comment->body = $validated['body'];
        $comment->user_id = Auth::id();
        $comment->post_id = $post->id;

        if ($request->has('parent_id')) {
            $comment->parent_id = $validated['parent_id'];
        }

        $comment->save();
        $comment->load('user');

        if ($request->ajax()) {
            return response()->json($comment);
        }

        return back()->with('success', 'Комментарий добавлен!');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Обновление комментария
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'body' => 'required|min:3|max:1000'
        ]);

        $comment->update($validated);

        if ($request->ajax()) {
            return response()->json($comment);
        }

        return back()->with('success', 'Комментарий обновлен!');
    }


    /**
     * Удаление комментария (мягкое удаление)
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Комментарий удален']);
        }

        return back()->with('success', 'Комментарий удален!');
    }

}
