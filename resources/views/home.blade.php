
@extends("layouts.app")
@section("content")
    Мой блог на Laravel
    Добро пожаловать в мой блог!
    @foreach($posts as $post)
        {{ $post['title'] }}
        {{ Str::limit($post['content'], 150) }}
        Читать далее...
    @endforeach
@endsection
