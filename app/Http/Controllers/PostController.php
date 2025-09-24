<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostController extends SiteController
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
//        return view("posts.create");
        // теги
//        $tags = ["PHP", "Laravel", "JavaScript", "Vue", "React",
//            "CSS", "HTML"];
//
//        return view('admin.posts.create', $tags);

        $tags = Tag::all();
//        $tags = Tag::pluck('tags.name')->toArray();
        $saved_tags = [];
        // Получение текущего пользователя
//        $user = auth()->user();
//        $userTags = $user->tags()->pluck('tags.id')->toArray();

        return view('admin.posts.create', compact('tags',
            'saved_tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            "title.required" => "Заголовок обязателен для заполнения",
            "content.min" => "Содержание должно быть не менее :min символов"
        ];

        $validated = $request->validate([
            "title" => "required|min:3|max:255",
            "content" => "required|min:10",
            "tags" => "array",
            "tags.*" => "exists:tags,id",
            "published_at" => "nullable|date",
        ], $messages);

        // Данные прошли валидацию
        $title = $validated['title'];
        $content = $validated['content'];
        $post = [
            "title" => $title,
            "slug" => Str::slug($title),
            "content" => $content,
            "excerpt" => Str::limit($validated['content'], 150),
            "user_id" => Auth::user()->id,
            "views" => rand(0, 1000),
            "published" => true,
            "published_at" => now(),
            "created_at" => now(),
            "updated_at" => now(),
        ];
//        $post = array_merge($validated,$post);
//        $postId = 1;
        $postId = DB::table("posts")->insertGetId($post);

//        $post = Post::create($post);
        $tags =  json_encode($request->only(['tags']));
    // Добавляем теги к статье
    //        $requestTags = json_encode($request->only(['tags']));//[];
        $requestTags = $request->only(['tags']);

        foreach ($requestTags['tags'] as $tagIndex) {
            $postTags[] = [
                "post_id" => $postId,
                "tag_id" => $tagIndex,
                "created_at" => now(),
                "updated_at" => now(),
            ];
        }
        DB::table("post_tag")->insert($postTags);
//        auth()->user()->posts()->create($validated);

//        return redirect()->route("posts.show", $post)
//            ->with("success", "Статья успешно создана!");
        return redirect()->route('admin.posts.create')
            ->with('success', 'Новость успешно добавлена!');


//        return redirect()->route("posts.index")
//            ->with("success", "Статья успешно создана!");
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

    /*
 * Транслит для ЧПУ
 */
    public function translit_sef($value)
    {
        $converter = array(
            'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
            'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
            'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
            'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
            'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
            'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
            'э' => 'e',    'ю' => 'yu',   'я' => 'ya',
        );

        $value = mb_strtolower($value);
        $value = strtr($value, $converter);
        $value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
        $value = mb_ereg_replace('[-]+', '-', $value);
        $value = trim($value, '-');

        return $value;
    }
}
