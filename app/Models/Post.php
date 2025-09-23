<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use \App\Models\User;

class Post extends Model
{
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

}
