<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (! auth()->check() || ! auth()->user()->is_admin) {
            abort(403, 'Доступ запрещен');
        }

        return $next($request);
    }
}
