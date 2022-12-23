<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tag;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class TagPolicy
{
    use HandlesAuthorization;

    public function unFollow(User $user, UserTag $tag)
    {
      return $user->user_id == $tag->user_id;
    }

    /**
     * Checks if a user can create a tag
     * 
     * @param User User to check.
     * @return Bool True if the user is an admin, false otherwise.
     */
    public function create(User $user)
    {
      return $user->is_admin();
    }
}
