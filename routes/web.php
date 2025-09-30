<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Admin\CommentModerationController;
use Illuminate\Support\Facades\Route;

// Маршруты для сайта
Route::get('/home', [SiteController::class, 'index'])->name('home');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [SiteController::class, 'submitContactForm'])->name('contact.submit');

// Маршруты для админки
Route::prefix('admin')->group(function () {
    // Маршруты для входа
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    // Защита маршрутов админки
    Route::middleware('auth')->group(function () {
//        Route::get('/news/create', [NewsController::class, 'create'])->name('admin.news.create');
//        Route::post('/news', [NewsController::class, 'store'])->name('admin.news.store');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        Route::middleware('admin')->group(function () { // работает!!! 26/09
            Route::get("/posts/create", [PostController::class, "create"])->name('admin.posts.create');
            Route::post("/posts/store", [PostController::class, "store"])->name('admin.posts.store');
//        Route::get("/posts/{id}", [PostController::class, "show"])->name('posts.show');
            Route::get("/posts/{id}/edit", [PostController::class, "edit"]);
            Route::put("/posts/{id}", [PostController::class, "update"]);
            Route::delete("/posts/{id}", [PostController::class, "destroy"]);
//        Route::get('/comments', [CommentModerationController::class, 'index'])->name('admin.comments.index');
        });
    });
});
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

// Отображение всех статей
//Route::get("/posts", function () {
//    return "Список всех статей блога";
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

// Страница "О нас"
//Route::get("/about", function () {
////    return "Обо мне";
//    return view("about");
//})->name('about');
//
//// Страница контактов (/contact)
//Route::get("/contact", function () {
////    return "Обо мне";
//    return view("contact");
//})->name('contact');

Route::get("/posts", [PostController::class, "index"])->name('posts');;
//Route::get("/posts/create", [PostController::class, "create"]);
//Route::post("/posts", [PostController::class, "store"]);
Route::get("/posts/{id}", [PostController::class, "show"])->name('posts.show');
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

// Временное решение - закомментируйте сложную проверку
//Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
// Или проверяйте просто на is_admin
//    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
//        Route::get('/comments', [CommentModerationController::class, 'index'])
//            ->name('comments.index')
//            ->middleware('can:is_admin'); // если у вас есть такой gate
//    });
//Route::middleware(['auth', 'admin'])->get('admin/comments', [CommentModerationController::class, 'index'])->name('admin.comments.index');
