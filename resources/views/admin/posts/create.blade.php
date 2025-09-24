<!-- resources/views/admin/news/create.blade.php -->

@extends('layouts.app')

@section('title', 'Создать публикацию')

@section('content')
<div class="container">
    <h1>Добавить статью</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.posts.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Заголовок</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="content">Содержание</label>
            <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="tags[]">Тэги</label>
            @foreach ($tags as $tag)
                <label>
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, $saved_tags) ? 'checked' : '' }}> {{ $tag->name }}
                </label>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary">Сохранить новость</button>
    </form>
</div>
@endsection
