<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return false;
    }

    public function moderate(User $user): bool
    {
        return /*$user->isAdmin ||*/ $user->isModerator();
    }

    public function viewAny(User $user): bool
    {
        return $this->moderate($user);
    }

    public function view(User $user, Comment $comment): bool
    {
        return $this->moderate($user);
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isModerator();
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $this->moderate($user);
    }

    public function approve(User $user, Comment $comment): bool
    {
        return $this->moderate($user);
    }

    public function reject(User $user, Comment $comment): bool
    {
        return $this->moderate($user);
    }

}
