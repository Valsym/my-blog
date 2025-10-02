<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

//use \App\Models\User;

class Post extends Model
{
//    protected $fillable = [
//        'title',
//        'content',
//        'slug',
//        'user_id',
//        'excerpt',
//        'views',
//        'published'
//    ];
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
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Константы для статусов
    const STATUS_PUBLISHED = 'published';
    const STATUS_MODERATION = 'moderation';
    const STATUS_DRAFT = 'draft';

    // Если нужно, добавьте мутатор для очистки HTML
    public function setContentAttribute($value)
    {
        // Очистка от потенциально опасного HTML, если нужно
//        $this->attributes['content'] = $value;
    }

    public static function findOrFail(int $id)
    {
        //
    }

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

}
