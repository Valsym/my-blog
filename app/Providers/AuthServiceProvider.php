<?php

namespace App\Providers;

use App\Models\Comment;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Comment::class => CommentPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Определяем gate для модерации комментариев
        /*Gate::define('moderate comments', function ($user) {
            return $user->isModerator(); // Используем метод из модели User
        });

        // Дополнительные gates для конкретных действий
        Gate::define('view comment moderation', function ($user) {
            return $user->isModerator();
        });

        Gate::define('approve comments', function ($user) {
            return $user->isModerator();
        });

        Gate::define('reject comments', function ($user) {
            return $user->isModerator();
        });

        // В методе boot() AuthServiceProvider
        Gate::define('is_admin', function ($user) {
            return $user->is_admin === true;
        });

        Gate::define('is_moderator', function ($user) {
            return $user->isModerator();
        });*/
    }
}
