<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AdminPostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['categories', 'tags', 'user'])
            ->latest()
            ->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'published' => 'required|in:published,moderation,draft',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        // Генерация slug
        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'user_id' => Auth::id(),
            'published' => $validated['published'],
            'created_at' => $validated['created_at'] ?? now(),
            'updated_at' => $validated['updated_at'] ?? now(),
        ]);

        // Привязываем категории и теги
        if ($request->has('categories')) {
            $post->categories()->sync($request->categories);
        }

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Пост успешно создан!');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.posts.create'/*edit'*/, compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
//        dd($request->all()); // отладка

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'published' => 'required|in:published,moderation,draft',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'],
            'published' => $validated['published'],
            'created_at' => $validated['created_at'],
            'updated_at' => $validated['updated_at'],
        ]);

        // Синхронизируем категории и теги
//        $post->categories()->sync($request->categories ?? []);
//        $post->tags()->sync($request->tags ?? []);
        $post->categories()->sync($request->input('categories', []));
        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('admin.posts.index')
            ->with('success', 'Пост успешно обновлен!');
    }

    public function destroy(Post $post)
    {
        $post->categories()->detach();
        $post->tags()->detach();
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Пост успешно удален!');
    }
}
