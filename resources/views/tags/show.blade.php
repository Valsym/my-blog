@extends("layouts.app")
@section("content")
    <div class="row">
        <div class="col-md-8">
            <h1>Тег: {{ $tag->name }}</h1>
            <p class="text-muted">Количество постов: {{ $posts->total() }}</p>

            @foreach($posts as $post)
                <!-- Тот же код карточки поста что и в index.blade.php -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">
                                👤 автор: {{ $post->user->name }}
                                | 👁 просмотры: {{ $post->views }}
                                | 📅 {{ $post->created_at->format('d.m.Y') }}
                            </span>
                            </div>

                            <h5 class="card-title">{{ $post->title }}</h5>

                            <div class="card-text">
                                {!! Str::limit(strip_tags($post->content), 200) !!}
                            </div>

                            <div class="mt-3">
                                @foreach($post->tags as $tag)
                                    <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                                @endforeach
                            </div>

                            <a class="btn btn-outline-primary mt-3"
                               href="{{ route('public.posts.show',['id' => $post->id]) }}">
                                Читать далее...
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Пагинация -->
            <div class="d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        </div>

        <!-- Сайдбар -->
        @include('partials.sidebar')
    </div>
@endsection
