
@extends("layouts.app")
@section("content")
    <h1>Мой блог на Laravel</h1>
    <br>
    <h2>Добро пожаловать в мой блог!</h2>

    <!-- Блок поиска (для мобильных устройств) -->
    <div class="d-block d-md-none mb-4">
        <form action="{{ route('public.posts.index') }}" method="GET" class="d-flex">
            <input type="text" class="form-control me-2" name="search"
                   placeholder="Поиск по блогу..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Найти</button>
        </form>
        @if(request('search'))
            <div class="mt-2">
                <small class="text-muted">
                    Результаты поиска для: "{{ request('search') }}"
                    <a href="{{ route('public.posts.index') }}" class="text-danger ms-2">✕ Сбросить</a>
                </small>
            </div>
        @endif
    </div>

    <div class="row">
        <!-- Основной контент -->
        <div class="col-md-8">
            @if(request('search'))
                <div class="alert alert-info">
                    <h4>Результаты поиска для: "{{ request('search') }}"</h4>
                    <p class="mb-0">Найдено постов: {{ $posts->total() }}</p>
                </div>
            @endif

            <h2 class="text-center">Новости</h2>

            @foreach($posts as $post)
                <div class="card mb-4">
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
            @endforeach

            <!-- Пагинация -->
            <div class="d-flex justify-content-center">
                {{ $posts->appends(['search' => request('search')])->links() }}
            </div>

            @if($posts->isEmpty())
                <div class="alert alert-warning text-center">
                    @if(request('search'))
                        По вашему запросу "{{ request('search') }}" ничего не найдено.
                    @else
                        Нет статей для отображения.
                    @endif
                </div>
            @endif
        </div>

        <!-- Сайдбар -->
        @include('partials.sidebar')
    </div>
@endsection
