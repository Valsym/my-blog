<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'slug',
        'user_id',
        'excerpt',
        'views',
        'published',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Константы для статусов
    const STATUS_PUBLISHED = 'published';

    const STATUS_MODERATION = 'moderation';

    const STATUS_DRAFT = 'draft';

    /**
     * Post принадлежит одному User'у
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Многие-ко-многим
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Комментарии к посту
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->root();
    }

    /**
     * Все комментарии (включая ответы)
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // Scope для опубликованных постов
    public function scopePublished($query)
    {
        return $query->where('published', self::STATUS_PUBLISHED);
    }

    // Для фильтров в админке
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%");
        });
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('published', $status);
    }

    public function scopeByCategories($query, $categoryIds)
    {
        return $query->whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereIn('categories.id', $categoryIds);
        });
    }

    public function scopeByTags($query, $tagIds)
    {
        return $query->whereHas('tags', function ($q) use ($tagIds) {
            $q->whereIn('tags.id', $tagIds);
        });
    }

    public function scopeDateFrom($query, $date)
    {
        return $query->whereDate('created_at', '>=', $date);
    }

    public function scopeDateTo($query, $date)
    {
        return $query->whereDate('created_at', '<=', $date);
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }
}
