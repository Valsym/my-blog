<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Routing\Controller;
//use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    // Метод для отображения главной страницы
    public function index()
    {
        $posts = Post::with("user")->latest()->paginate(10);
//        User::find($post->user_id);

        return view("home", compact("posts"));
        // Получаем последние 5 новостей
//        $news = News::orderBy('created_at', 'desc')->take(5)->get();
//
//        return view('site.index', compact('news'));
    }

    // Метод для отображения страницы "О нас"
    public function about()
    {
        return view('about');
    }

    // Метод для отображения страницы контактов
    public function contact()
    {
        return view('contact');
    }

    public function submitContactForm(Request $request)
    {
        $name = $request->input('name');
        $email  = $request->input('email');
        $message  = $request->input('message');

        return view('result', [
            'name' => $name, 'email' => $email, 'message' => $message
        ]);
    }
}
