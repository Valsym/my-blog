<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin_login');
    }

    public function login(Request $request)
    {
        // Проверка учетных данных с использованием базы данных
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Проверяем, является ли пользователь администратором
            $user = Auth::user();

            if ($user->is_admin) {
                // Сохраняем информацию о том, что пользователь админ
                session(['is_admin' => true]);
                //dd('ok!');

                return redirect()->route('admin.posts.create')
                    ->with('success', 'Вы успешно вошли в админку!');
            } else if ($user->is_moderator) {
                // Сохраняем информацию о том, что пользователь is_moderator
                session(['is_moderator' => true]);
                //dd('ok!');

                return redirect()->route('admin.posts.create')
                    ->with('success', 'Вы успешно вошли в админку!');
            } else {
                Auth::logout();

                return redirect()->back()->withErrors(['email' =>
                    'У вас нет прав администратора.']);
            }
        }

        return redirect()->back()->withErrors(['username' => 'Неверные учетные данные.']);
    }

    public function logout()
    {
        session()->forget(['is_admin', 'is_moderator']); // Удаляем сессию
        return redirect('/')->with('success', 'Вы вышли из админки!');
    }
}
