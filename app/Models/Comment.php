<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use League\CommonMark\CommonMarkConverter;

class Comment extends Model
{
    use SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // было:
//    protected $fillable = [
//        'body',
//        'user_id',
//        'post_id',
//        'parent_id'
//    ];
    protected $fillable = [
        'body',
        'user_id',
        'post_id',
        'parent_id',
        'status',
        'moderation_notes',
        'moderated_by',
        'moderated_at',
        'is_edited'
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
        'is_edited' => 'boolean',
    ];

    //protected $with = ['user'];


    protected $with = ['user', 'replies'];

    public function getBodyHtmlAttribute()
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $converter->convertToHtml($this->body);
    }

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

    // Добавлено 26/09/2025 для Системы Модерирования комментов
    /**
     * Scope для получения только одобренных комментариев
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope для получения ожидающих модерации
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope для получения отклоненных
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Проверяет, одобрен ли комментарий
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Проверяет, ожидает ли модерации
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Проверяет, отклонен ли комментарий
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Модератор, который обработал комментарий
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Автоматическая модерация на основе содержимого
     */
    public function shouldBeAutoApproved(): bool
    {
        // Автоматически одобряем комментарии от доверенных пользователей
        if ($this->user && $this->user->is_trusted) {
            return true;
        }

        // Проверяем на спам и запрещенные слова
        $spamWords = config('moderation.spam_words', []);
        $body = strtolower($this->body);

        foreach ($spamWords as $word) {
            if (str_contains($body, strtolower($word))) {
                return false;
            }
        }

        // Проверяем длину комментария
        if (strlen($this->body) < 10) {
            return false; // Слишком короткие комментарии
        }

        return true;
    }

}
