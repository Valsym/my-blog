<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'body',
        'user_id',
        'post_id',
        'parent_id'
    ];

    protected $with = ['user', 'replies'];

    /**
     * Пользователь, оставивший комментарий
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Пост, к которому относится комментарий
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Родительский комментарий (если это ответ)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Ответы на этот комментарий
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Проверяет, является ли комментарий ответом
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Проверяет, имеет ли комментарий ответы
     */
    public function hasReplies(): bool
    {
        return $this->replies->count() > 0;
    }

    /**
     * Scope для получения только корневых комментариев (не ответов)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
