<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

//use \App\Models\User;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'slug',
        'user_id',
        'excerpt',
        'views',
        'published'
    ];

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


}
