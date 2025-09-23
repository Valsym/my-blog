<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});
//Route::get('/', function () {
//    return "Добро пожаловать в мой блог!";
//});
Route::get("/", function () {
    $posts = [
        ["id" => 1, "title" => "Первая статья", "content" =>
            "Содержание первой статьи..."],
        ["id" => 2, "title" => "Вторая статья", "content" =>
            "Содержание второй статьи..."],
    ];
//    dd($posts);
    return view("home", ["posts" => $posts]);
})->name('home');

// Отображение всех статей
Route::get("/posts", function () {
    return "Список всех статей блога";
});
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
Route::get("/about", function () {
//    return "Обо мне";
    return view("about");
})->name('about');

// Страница контактов (/contact)
Route::get("/contact", function () {
//    return "Обо мне";
    return view("contact");
})->name('contact');

//Route::get("/posts", [PostController::class, "index"]);
Route::get("/posts/create", [PostController::class, "create"]);
Route::post("/posts", [PostController::class, "store"]);
//Route::get("/posts/{id}", [PostController::class, "show"]);
Route::get("/posts/{id}/edit", [PostController::class, "edit"]);
Route::put("/posts/{id}", [PostController::class, "update"]);
Route::delete("/posts/{id}", [PostController::class, "destroy"]);

// Страница категорий статей (/categories)
