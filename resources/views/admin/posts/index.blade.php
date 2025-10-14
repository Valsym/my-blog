@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Управление постами</h1>
                    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                        Создать пост
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Форма фильтрации -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Фильтры и поиск</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.posts.index') }}">
                            <div class="row">
                                <!-- Поиск -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="search" class="form-label">Поиск</label>
                                        <input type="text" class="form-control" id="search" name="search"
                                               value="{{ request('search') }}" placeholder="По заголовку, содержанию...">
                                    </div>
                                </div>

                                <!-- Статус -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Статус</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="">Все статусы</option>
                                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Опубликован</option>
                                            <option value="moderation" {{ request('status') == 'moderation' ? 'selected' : '' }}>На модерации</option>
                                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Черновик</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Сортировка -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="sort" class="form-label">Сортировка</label>
                                        <select class="form-select" id="sort" name="sort">
                                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>По дате создания</option>
                                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>По заголовку</option>
                                            <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>По просмотрам</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Категории -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Категории</label>
                                        <div style="max-height: 150px; overflow-y: auto; border: 1px solid #dee2e6; padding: 10px; border-radius: 4px;">
                                            @foreach($categories as $category)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categories[]"
                                                           value="{{ $category->id }}" id="filter_category_{{ $category->id }}"
                                                        {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="filter_category_{{ $category->id }}">
                                                        {{ $category->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Теги -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Теги</label>
                                        <div style="max-height: 150px; overflow-y: auto; border: 1px solid #dee2e6; padding: 10px; border-radius: 4px;">
                                            @foreach($tags as $tag)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="tags[]"
                                                           value="{{ $tag->id }}" id="filter_tag_{{ $tag->id }}"
                                                        {{ in_array($tag->id, request('tags', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="filter_tag_{{ $tag->id }}">
                                                        {{ $tag->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Даты -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="date_from" class="form-label">Дата от</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from"
                                               value="{{ request('date_from') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="date_to" class="form-label">Дата до</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to"
                                               value="{{ request('date_to') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Применить фильтры</button>
                                    <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Сбросить</a>

                                    <!-- Счетчик найденных постов -->
                                    @if(request()->anyFilled(['search', 'status', 'categories', 'tags', 'date_from', 'date_to']))
                                        <span class="ms-3 text-muted">
                                        Найдено постов: {{ $posts->total() }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Таблица постов -->
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Заголовок</th>
                                <th>Категории</th>
                                <th>Статус</th>
                                <th>Дата создания</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($posts as $post)
                                <tr>
                                    <td>{{ $post->id }}</td>
                                    <td>{{ $post->title }}</td>
                                    <td>
                                        @foreach($post->categories as $category)
                                            <span class="badge bg-secondary">{{ $category->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($post->published == 'published')
                                            <span class="badge bg-success">Опубликован</span>
                                        @elseif($post->published == 'moderation')
                                            <span class="badge bg-warning">На модерации</span>
                                        @else
                                            <span class="badge bg-secondary">Черновик</span>
                                        @endif
                                    </td>
                                    <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-primary">
                                            Редактировать
                                        </a>
                                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить пост?')">
                                                Удалить
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
