<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class Authenticate
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
        //check here if the user is authenticated
//        if ( ! $this->auth->user() )
        if (!$request->user()) {
            // here you should redirect to login
            return redirect()->route('admin.login')
                ->with(['error', 'Требуется авторизация.']);
        }
        return $next($request);

    }
}
