<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property bool $is_admin
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_trusted
 * @property bool $is_moderator
 * @property bool $is_blocked
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $moderatedComments
 * @property-read int|null $moderated_comments_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User admins()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsModerator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsTrusted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
