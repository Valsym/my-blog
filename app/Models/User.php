<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [ // 26/09/2025
        'name', 'email', 'password', 'is_trusted', 'is_moderator',
        'is_admin', 'is_blocked',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [ // 26/09/2025
        'email_verified_at' => 'datetime',
        'is_trusted' => 'boolean',
        'is_moderator' => 'boolean',
        'is_admin' => 'boolean',
        'is_blocked' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Комментарии пользователя
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Проверяет, является ли пользователь доверенным
     */
    public function isTrusted(): bool
    {
        return $this->is_trusted || $this->is_admin || $this->is_moderator;
    }

    /**
     * Проверяет, является ли пользователь модератором
     */
    public function isModerator(): bool
    {
        return $this->is_moderator || $this->is_admin;
    }

    /**
     * Комментарии, которые пользователь модерировал
     */
    public function moderatedComments()
    {
        return $this->hasMany(Comment::class, 'moderated_by');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function isBlocked()
    {
        return $this->is_blocked;
    }

    // Scope для админов
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    // Scope для активных пользователей
    public function scopeActive($query)
    {
        return $query->where('is_blocked', false);
    }
}
