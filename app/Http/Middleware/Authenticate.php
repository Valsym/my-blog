<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Проверяет, является ли пользователь админом
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()) {

            return redirect()->route('admin.login')
                ->with(['error', 'Требуется авторизация.']);
        }

        return $next($request);

    }
}
