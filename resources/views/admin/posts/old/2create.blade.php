{{-- resources/views/admin/posts/create.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ isset($post) ? 'Редактирование поста' : 'Создание поста' }}</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($post) ? route('admin.posts.update', $post) : route('admin.posts.store') }}" method="POST">
                            @csrf
                            @if(isset($post))
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label for="title" class="form-label">Заголовок *</label>
                                <input type="text" class="form-control" id="title" name="title"
                                       value="{{ old('title', $post->title ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Контент *</label>
                                <textarea class="form-control" id="content" name="content" rows="15" required>{{ old('content', $post->content ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Краткое описание</label>
                                <textarea class="form-control" id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="created_at" class="form-label">Дата создания</label>
                                        <input type="datetime-local" class="form-control" id="created_at" name="created_at"
                                               value="{{ old('created_at', isset($post) ? $post->created_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="updated_at" class="form-label">Дата обновления</label>
                                        <input type="datetime-local" class="form-control" id="updated_at" name="updated_at"
                                               value="{{ old('updated_at', isset($post) ? $post->updated_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ isset($post) ? 'Обновить пост' : 'Создать пост' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Настройки поста</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="published" class="form-label">Статус</label>
                            <select class="form-select" id="published" name="published">
                                <option value="published" {{ old('published', $post->published ?? '') == 'published' ? 'selected' : '' }}>
                                    Опубликован
                                </option>
                                <option value="moderation" {{ old('published', $post->published ?? '') == 'moderation' ? 'selected' : '' }}>
                                    На модерации
                                </option>
                                <option value="draft" {{ old('published', $post->published ?? '') == 'draft' ? 'selected' : '' }}>
                                    Черновик
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Категории</label>
                            @foreach($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]"
                                           value="{{ $category->id }}" id="category{{ $category->id }}"
                                        {{ in_array($category->id, old('categories', isset($post) ? $post->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Теги</label>
                            @foreach($tags as $tag)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tags[]"
                                           value="{{ $tag->id }}" id="tag{{ $tag->id }}"
                                        {{ in_array($tag->id, old('tags', isset($post) ? $post->tags->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tag{{ $tag->id }}">
                                        {{ $tag->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
