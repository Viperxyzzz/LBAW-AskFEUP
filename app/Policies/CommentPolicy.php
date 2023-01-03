<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Check if the user can create a comment.
     * Every logged user can create a comment.
     * @param User $user User to check
     * @return bool True if the user can create comments.
     */
    public function create(User $user)
    {
      return Auth::check();
    }

    /**
     * Check which users can edit the comment.
     * Only a comment's author and admins can edit it
     * @param User $user User to check.
     * @param Comment comment to check.
     * @return bool True if the user can edit the comment.
     */
    public function edit(User $user, Comment $comment)
    {
      return $user->user_id == $comment->user_id || $user->is_admin;
    }

    /**
     * Check which users can edit the comment.
     * Only a comment's author and admins can edit it
     * @param User $user User to check.
     * @param Comment comment to check.
     * @return bool True if the user can edit the comment.
     */
    public function update(User $user, Comment $comment)
    {
      return $user->user_id == $comment->user_id || $user->is_admin;
    }

    /**
     * Check which users can delete the comment.
     * Only a comment's author and admins can delete it
     * @param User $user User to check.
     * @param Comment comment to check.
     * @return bool True if the user can delete the comment.
     */
    public function delete(User $user, Comment $comment)
    {
      return $user->user_id == $comment->user_id || $user->is_admin;
    }

    /**
     * Check if a user can vote on a comment.
     * All logged users can vote on comments.
     * @param User $user User to check.
     * @param Comment $comment Comment to check.
     * @return bool True if the user can vote on the comment, false otherwise.
     */
    public function vote(User $user, Comment $comment)
    {
      return Auth::check();
    }
}
