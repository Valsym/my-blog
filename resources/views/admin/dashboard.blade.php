@extends('layouts.admin')

@section('title', 'Админ-панель')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4">Админ-панель</h1>
            </div>
        </div>

        <!-- Статистика -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Всего постов</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $postsCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-newspaper fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Опубликовано</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $publishedPostsCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Комментарии</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $commentsCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-comments fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Пользователи</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $usersCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Быстрые ссылки -->
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-newspaper fa-3x mr-3"></i>
                            <div>
                                <div class="text-white-50 small">Управление</div>
                                <div class="h5 font-weight-bold">Постами</div>
                            </div>
                        </div>
                        <a href="{{ route('admin.posts.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card bg-success text-white shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-comments fa-3x mr-3"></i>
                            <div>
                                <div class="text-white-50 small">Управление</div>
                                <div class="h5 font-weight-bold">Комментариями</div>
                            </div>
                        </div>
                        <a href="{{ route('admin.comments.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card bg-info text-white shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users fa-3x mr-3"></i>
                            <div>
                                <div class="text-white-50 small">Управление</div>
                                <div class="h5 font-weight-bold">Пользователями</div>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card bg-warning text-white shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-cog fa-3x mr-3"></i>
                            <div>
                                <div class="text-white-50 small">Мой</div>
                                <div class="h5 font-weight-bold">Профиль</div>
                            </div>
                        </div>
                        <a href="{{ route('admin.profile.edit') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Последние посты -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Последние посты</h6>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-primary">Все посты</a>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($recentPosts as $post)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $post->title }}</h6>
                                        <small class="text-muted">
                                            {{ $post->user->name }} •
                                            {{ $post->created_at->format('d.m.Y H:i') }} •
                                            <span class="badge badge-{{ $post->published === 'published' ? 'success' : ($post->published === 'draft' ? 'warning' : 'secondary') }}">
                                            {{ $post->published }}
                                        </span>
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-primary">Редактировать</a>
                                        <a href="{{ route('public.posts.show', $post->slug) }}" class="btn btn-sm btn-outline-secondary" target="_blank">Просмотр</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .stretched-link::after {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1;
            content: "";
        }
    </style>
@endsection
