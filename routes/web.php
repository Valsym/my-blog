<?php

use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Admin\CommentModerationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


//Route::get("/", function () {
//    $posts = [
//        ["id" => 1, "title" => "Первая статья", "content" =>
//            "Содержание первой статьи..."],
//        ["id" => 2, "title" => "Вторая статья", "content" =>
//            "Содержание второй статьи..."],
//    ];
////    dd($posts);
//    return view("home", ["posts" => $posts]);
//})->name('home');
//
////Отображение всех статей
//Route::get("/all", function () {
//    return "Список всех статей блога";
//});


// Маршруты для сайта
Route::get('/home', [SiteController::class, 'index'])->name('home');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [SiteController::class, 'submitContactForm'])->name('contact.submit');

// Маршруты для админки
Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('auth.register');
Route::post('/register', [AdminAuthController::class, 'register'])->name('register');

Route::prefix('admin')->group(function () {
    // Маршруты для входа
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    // Защита маршрутов админки
    Route::middleware('auth')->group(function () {
//        Route::get('/news/create', [NewsController::class, 'create'])->name('admin.news.create');
//        Route::post('/news', [NewsController::class, 'store'])->name('admin.news.store');
        Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        Route::middleware('admin')->name('admin.')->group(function () { // работает!!! 26/09
            // Админка постов 01/10/2025
            Route::resource('posts', AdminPostController::class)
                ->except(['show']);
            Route::post('/posts/upload', [AdminPostController::class, 'upload'])
                ->name('posts.upload');

            //            Route::get("/posts/create", [PostController::class, "create"])->name('admin.posts.create');
//            Route::post("/posts/store", [PostController::class, "store"])->name('admin.posts.store');
////        Route::get("/posts/{id}", [PostController::class, "show"])->name('posts.show');
//            Route::get("/posts/{id}/edit", [PostController::class, "edit"]);
//            Route::put("/posts/{id}", [PostController::class, "update"]);
//            Route::delete("/posts/{id}", [PostController::class, "destroy"]);
        });
    });
});
//Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
//    Route::resource('posts', AdminPostController::class)->except(['show']);
//});

// Отображение одной статьи
Route::get("/posts/{id}", function ($id) {
    return "Статья номер: " . $id;
});
// Маршрут с необязательным параметром
Route::get("/user/{name?}", function ($name = "Гость") {
    return "Привет, " . $name . "! Добро пожаловать в блог!";
});

Route::prefix("admin")->group(function () {
    Route::get("/dashboard", function () {
        return "Админ-панель";
    });
    Route::get("/users", function () {
        return "Список пользователей";
    });
});

Route::get("/posts", [PostController::class, "index"])->name('public.posts.index');
//Route::get("/posts/create", [PostController::class, "create"]);
//Route::post("/posts", [PostController::class, "store"]);
Route::get("/posts/{id}", [PostController::class, "show"])->name('public.posts.show');

Route::get("/posts/{tag:slug}", [PostController::class, "show"])->name('public.posts.show');

//Route::get("/posts/{id}/edit", [PostController::class, "edit"]);
//Route::put("/posts/{id}", [PostController::class, "update"]);
//Route::delete("/posts/{id}", [PostController::class, "destroy"]);

// Страница категорий статей (/categories)

// Комментарии
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
    ->name('comments.store');
Route::prefix('/comments')->group(function () {
    Route::put('/{comment}', [CommentController::class, 'update'])
        ->name('comments.update');

    Route::delete('/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');
});

// Маршруты модерации
// было... стало:
// Маршруты модерации - используем более конкретный gate
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'/*'can:moderate'/*'can:view comment moderation'*/])->group(function () {
    Route::get('/comments', [CommentModerationController::class, 'index'])->name('comments.index');
    Route::get('/comments/{comment}', [CommentModerationController::class, 'show'])->name('comments.show');

    // Защищаем отдельные действия более специфичными gates
//    Route::middleware('can:approve comments')->group(function () {
        Route::post('/comments/{comment}/approve', [CommentModerationController::class, 'approve'])->name('comments.approve');
//    });

//    Route::/*middleware('can:reject comments')->*/group(function () {
        Route::post('/comments/{comment}/reject', [CommentModerationController::class, 'reject'])->name('comments.reject');
        Route::post('/comments/bulk-action', [CommentModerationController::class, 'bulkAction'])->name('comments.bulk-action');
//    });
});

// Маршруты для категорий
//Route::get('/categories/{id}', [App\Http\Controllers\CategoryController::class, 'show'])
//    ->name('public.categories.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])
    ->name('public.categories.show');

// Маршруты для тегов
//Route::get('/tags/{id}', [App\Http\Controllers\TagController::class, 'show'])
//    ->name('public.tags.show');
Route::get('/tags/{tag:slug}', [App\Http\Controllers\TagController::class, 'show'])
    ->name('public.tags.show');

// Админ-маршруты
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [DashboardController::class, 'index']); // Перенаправление на дашборд

    // Посты
    Route::resource('posts', AdminPostController::class);

    // Комментарии
//    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
//    Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

    // Пользователи
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('users.toggle-block');

    // Профиль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
