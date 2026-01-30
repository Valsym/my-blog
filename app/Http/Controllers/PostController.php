<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostController extends SiteController
{
    /**
     *  Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $query = Post::with('user', 'tags')
//            ->where('is_published', true);
            ->where('published', Post::STATUS_PUBLISHED);

        // Поиск по словам
        // Используем request() хелпер вместо инъекции
        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->latest()->paginate(10);

        // Данные для сайдбара - исправляем счетчики для категорий
        $categories = Category::withCount(['posts' => function ($query) {
            $query->where('published', Post::STATUS_PUBLISHED);
        }])->get();

        $tags = Tag::withCount(['posts' => function ($query) {
            $query->where('published', Post::STATUS_PUBLISHED);
        }])->get();

        $popularPosts = Post::where('published', Post::STATUS_PUBLISHED)// where('is_published', true)
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        return view('index', compact('posts', 'categories', 'tags', 'popularPosts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $tags = Tag::all();
        $saved_tags = [];

        return view('admin.posts.create', compact('tags',
            'saved_tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'title.required' => 'Заголовок обязателен для заполнения',
            'content.min' => 'Содержание должно быть не менее :min символов',
        ];

        $validated = $request->validate([
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:10',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'published_at' => 'nullable|date',
        ], $messages);

        // Данные прошли валидацию
        $title = $validated['title'];
        $content = $validated['content'];
        $post = [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => Str::limit($validated['content'], 150),
            'user_id' => Auth::user()->id,
            'views' => rand(0, 1000),
            'published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $postId = DB::table('posts')->insertGetId($post);

        $tags = json_encode($request->only(['tags']));

        $requestTags = $request->only(['tags']);

        foreach ($requestTags['tags'] as $tagIndex) {
            $postTags[] = [
                'post_id' => $postId,
                'tag_id' => $tagIndex,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('post_tag')->insert($postTags);

        return redirect()->route('admin.posts.create')
            ->with('success', 'Статья успешно создана!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)// int $id)//Post $post)
    {
        $post = Post::where('published', Post::STATUS_PUBLISHED)
            ->firstOrFail();

        // Увеличиваем счетчик просмотров
        $post->increment('views');

        $post = $post
            ->load('user', 'tags', 'comments.user', 'comments.replies.user');

        return view('posts.show', compact('post'));
    }
}
