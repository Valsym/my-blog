<div class="col-md-4">
    <!-- Поиск -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Поиск по блогу</h5>
            <form action="{{ route('public.posts.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" name="search"
                           placeholder="Введите слово..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Найти</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Категории -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Категории</h5>
            <ul class="list-unstyled">
                @foreach($categories as $category)
                    <li class="mb-2">
                        <a href="{{ route('public.categories.show', $category->slug) }}"
                           class="text-decoration-none">
                            {{ $category->name }}
                            <span class="badge bg-light float-end">( {{ $category->posts_count }} )</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Теги -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Теги</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <a href="{{ route('public.tags.show', $tag->slug) }}"
                       class="badge bg-light text-decoration-none">
                        {{ $tag->name }} ({{ $tag->posts_count }})
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Популярные посты -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Популярные посты</h5>
            <ul class="list-unstyled">
                @foreach($popularPosts as $post)
                    <li class="mb-3 border-bottom pb-2">
                        <a href="{{ route('public.posts.show', $post->id) }}"
                           class="text-decoration-none fw-bold">
                            {{ Str::limit($post->title, 50) }}
                        </a>
                        <div class="text-muted small">
                            <span>👁 {{ $post->views }} просмотров</span><br>
                            <span>📅 {{ $post->created_at->format('d.m.Y') }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
