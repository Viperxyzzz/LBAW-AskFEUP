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
     * Disabled users are not available too.
     */
    public function view(User $user, User $profile) {
      if ($profile->is_disable())
      return false;
      if ($profile->is_blocked()) {
        return $user->is_mod();
      }
    return true;
    }

    public function delete(User $user, User $profile)
    {
      // Only the admin and the user can delete their own account
      return $user->user_id == $profile->user_id || $user->is_admin;
    }

    public function edit(User $user, User $profile)
    {
      return $user->user_id === $profile->user_id || $user->is_admin;
    }
}
