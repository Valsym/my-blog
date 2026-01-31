<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $body
 * @property int $user_id
 * @property int $post_id
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $status
 * @property string|null $moderation_notes
 * @property int|null $moderated_by
 * @property \Illuminate\Support\Carbon|null $moderated_at
 * @property bool $is_edited
 * @property-read \App\Models\User|null $moderator
 * @property-read Comment|null $parent
 * @property-read \App\Models\Post $post
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment rejected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment root()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereIsEdited($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereModeratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereModeratedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereModerationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment withoutTrashed()
 * @mixin \Eloquent
 */
class Comment extends Model
{
    use SoftDeletes;

    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'body',
        'user_id',
        'post_id',
        'parent_id',
        'status',
        'moderation_notes',
        'moderated_by',
        'moderated_at',
        'is_edited',
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
        'is_edited' => 'boolean',
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
        return $this->hasMany(Comment::class, 'parent_id');
        // это нужно перенести в контроллер, т.к. вызывает
        // ошибку типизации в PHPStan
            //->orderBy('created_at', 'asc');
    }

    /**
     * Проверяет, является ли комментарий ответом
     */
    public function isReply(): bool
    {
        return ! is_null($this->parent_id);
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
        if (isset($this->user) && $this->user->is_trusted) {
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
