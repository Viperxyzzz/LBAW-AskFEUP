<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * See if a user can view another users profile.
     * This exists because blocked accounts are not available for regular users.
     */
    public function view(User $user, User $profile) {
      if ($profile->is_blocked()) {
        return $user->is_mod();
      }
      return true;
    }

    public function delete(User $user, User $profile)
    {
      // Only an answer's author can delete it
      //return $user->user_id == $answer->user_id || $user->is_admin;
      return false;
    }

    public function edit(User $user, User $profile)
    {
      return $user->user_id === $profile->user_id || $user->is_admin;
    }
}
