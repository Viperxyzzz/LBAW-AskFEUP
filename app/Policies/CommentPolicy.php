<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class CommentPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
      // Any user can create a new comment
      return Auth::check();
    }
    public function edit(User $user, Comment $comment)
    {
      // Only an answer's author can edit it
      return $user->user_id == $comment->user_id || $user->is_admin;
    }
}
