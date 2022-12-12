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

}
