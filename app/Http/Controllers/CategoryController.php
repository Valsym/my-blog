<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class CategoryController extends Controller
{
    public function show0($id)
    {
        $category = Category::findOrFail($id);
        $posts = Post::where('category_id', $id)
            ->where('published', Post::STATUS_PUBLISHED)
//            ->where('is_published', true)
            ->with('user', 'tags')
            ->latest()
            ->paginate(10);

        $categories = Category::withCount('posts')->get();
        $tags = Tag::withCount('posts')->get();
        $popularPosts = Post::where('published', Post::STATUS_PUBLISHED)//where('is_published', true)
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        return view('categories.show',
            compact('category', 'posts', 'categories', 'tags', 'popularPosts'));
    }

    public function show(Category $category)//$id
    {
//        $category = Category::findOrFail($id);

        // Получаем посты через отношение многие-ко-многим
        $posts = $category->posts()
            ->where('published', Post::STATUS_PUBLISHED)
            ->with('user', 'tags')
            ->latest()
            ->paginate(10);

        $categories = Category::withCount(['posts' => function($query) {
            $query->where('published', Post::STATUS_PUBLISHED);
        }])->get();

        $tags = Tag::withCount(['posts' => function($query) {
            $query->where('published', Post::STATUS_PUBLISHED);
        }])->get();

        $popularPosts = Post::where('published', Post::STATUS_PUBLISHED)
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        return view('categories.show',
            compact('category', 'posts', 'categories', 'tags', 'popularPosts'));
    }
}
