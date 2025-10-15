<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CheckAdminMiddleware
{

    /**
     * Проверяет, является ли пользователь админом
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next)//, string $role): Response
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Доступ запрещен');
        }

        return $next($request);

//        if (session('is_admin') || session('is_moderator')) {
//            return $next($request);
//
//        }
//
//        return redirect()->route('admin.login')
//            ->with('error', 'Требуется авторизация как модератор или админ');

    }
}
