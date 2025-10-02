@extends("layouts.app")
@section("content")
    <h1>{{ $post->title }}</h1>
    <br>

    <div class="row">
        <div class="col-md-12">

            <div class="card mb-3">
                <div class="card-body">
                    <span class="text-muted">автор: {{ $post->user->name }}  | views: {{ $post->views }}</span>
                    <div class="card-body">{!! $post->content !!}</div>
                    <span class="text-muted news-date">
                            {{ $post->created_at->format('d.m.Y') }} | tags: </span>
                    @foreach($post->tags as $tag)
                        <span> {{ $tag->name }} </span>
                    @endforeach
                </div>
            </div>

            <!-- Секция комментариев -->
            @include('posts._comments', ['post' => $post])

        </div>
    </div>
@endsection
