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
        if (!session('is_admin')) {
            return redirect()->route('admin.login')
                ->with('error', 'Требуется авторизация.');
        }
//          OR
//        if (!$request->user() || !$request->user()->isAdmin()) {
//            return response()->json(['message' => 'Forbidden'], 403);
//        }

        return $next($request);

    }
}
