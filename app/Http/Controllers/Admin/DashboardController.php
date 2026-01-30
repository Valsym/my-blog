<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $postsCount = Post::count();
        $publishedPostsCount = Post::where('published', 'published')->count();
        $commentsCount = Comment::count();
        $usersCount = User::count();
        $recentPosts = Post::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'postsCount',
            'publishedPostsCount',
            'commentsCount',
            'usersCount',
            'recentPosts'
        ));
    }
}
