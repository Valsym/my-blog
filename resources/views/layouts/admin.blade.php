<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield("title", "Мой блог")</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .tox-tinymce {
            border-radius: 4px;
        }
        .ck-editor__editable {
            min-height: 500px;
        }
        /* Стили для фильтров */
        .table th a {
            text-decoration: none;
            color: inherit;
        }

        .table th a:hover {
            color: #0d6efd;
        }

        .badge {
            margin: 1px;
        }

        .form-check {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="{{ route('home') }}">Админка Сайта</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">Главная</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('about') }}">Обо мне</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('contact') }}">Контакты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('public.posts.index') }}">Статьи</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.comments.index') }}">Модерация комментов</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.logout') }}">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    @yield('content')
</div>

<div class="text-center">© Мой блог, {{ date("Y") }}</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Секция для скриптов -->
@yield('scripts')
</body>
</html>
