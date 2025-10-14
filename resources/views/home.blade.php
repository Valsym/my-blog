
@extends("layouts.app")
@section("content")
    <h1>Мой блог на Laravel</h1>
    <br>
    <h2>Добро пожаловать в мой блог!</h2>

    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center">Новости</h2>

            @foreach($posts as $post)
                <div class="card mb-3">
                    <div class="card-body">
                        <span class="text-muted">автор: {{ $post->user->name }}  | views: {{ $post->views }}</span>
                        <h5 class="card-title">{{ $post->title }}</h5>
                        <p class="card-text">{{ Str::limit($post->content, 150) }}</p>
                        <span class="text-muted news-date">
                            {{ $post->created_at->format('d.m.Y') }} | tags: </span>
                        @foreach($post->tags as $tag)
                            <span> {{ $tag->name }} </span>
                        @endforeach
                        <a class="nav-link" href="{{ route('public.posts.show',['id' => $post->id]) }}">далее...</a>
                    </div>
                </div>
            @endforeach

            {{ $posts->links() }}

            @if($posts->isEmpty())
                <p class="text-center">Нет статей для отображения.</p>
            @endif
        </div>
    </div>
@endsection
