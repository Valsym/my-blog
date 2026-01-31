<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminMiddleware
{
    /**
     * Проверяет, является ли пользователь админом
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check() || ! User::find(Auth::id())->is_admin) {
            abort(403, 'Доступ запрещен');
        }

        return $next($request);
    }
}
