<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;

class TagController extends Controller
{
    public function show(Tag $tag)//$id)
    {
//        $tag = Tag::findOrFail($id);
        $posts = $tag->posts()
            ->where('published', Post::STATUS_PUBLISHED)//where('is_published', true)
            ->with('user', 'tags')
            ->latest()
            ->paginate(10);

        $categories = Category::withCount('posts')->get();
        $tags = Tag::withCount('posts')->get();
        $popularPosts = Post::where('published', Post::STATUS_PUBLISHED)//where('is_published', true)
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        return view('tags.show', compact('tag', 'posts', 'categories', 'tags', 'popularPosts'));
    }
}
