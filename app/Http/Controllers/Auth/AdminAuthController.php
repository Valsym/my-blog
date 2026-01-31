<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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
            $user = User::find(Auth::id());

            if ($user->is_admin) {
                // Сохраняем информацию о том, что пользователь админ
                session(['is_admin' => true]);

                return redirect()->route('admin.posts.index')// create')
                    ->with('success', 'Вы успешно вошли в админку!');
            } elseif ($user->is_moderator) {
                // Сохраняем информацию о том, что пользователь is_moderator
                session(['is_moderator' => true]);

                return redirect()->route('admin.posts.create')
                    ->with('success', 'Вы успешно вошли в админку!');
            } else {
                return view('auth.admin_login', [
                    'success' => $user->name.', Вы успешно вошли на сайт!',
                    'name' => $user->name,
                ]);
            }
        }

        return redirect()->back()->withErrors(['username' => 'Неверные учетные данные.']);
    }

    public function logout()
    {
        // Выход из аутентификации (убирает аутентификацию пользователя)
        Auth::logout();

        // Очищаем сессию от кастомных данных
        session()->forget(['is_admin', 'is_moderator']);

        // Инвалидируем текущую сессию и генерируем новую токен
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/home')->with('success', 'Вы вышли из админки!');
    }

    /**
     * Регистрация пользователя
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // 1. Валидация данных
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ], [
            'name.required' => 'Поле имя обязательно для заполнения',
            'email.required' => 'Поле email обязательно для заполнения',
            'email.email' => 'Введите корректный email адрес',
            'email.unique' => 'Пользователь с таким email уже существует',
            'password.required' => 'Поле пароль обязательно для заполнения',
            'password.confirmed' => 'Пароли не совпадают',
        ]);

        // 2. Если валидация не пройдена
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибки валидации',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // 3. Создание пользователя
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return view('auth.admin_login', [
                'success' => $user->name.' - вы успешно зарегистрированы!<br>Введите ваши данные',
            ]);

        } catch (\Exception $e) {
            // 5. Обработка ошибок базы данных
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании пользователя',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function showRegisterForm()
    {
        return view('auth.register_form');
    }
}
