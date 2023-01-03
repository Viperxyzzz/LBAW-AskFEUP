<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * See if a user can view another users profile.
     * This exists because blocked accounts are not available for regular users.
     *  * Disabled users are not available too.
     */
    public function view(User $user, User $profile) {
      if ($profile->is_disable())
        return $user->is_mod();
      if ($profile->is_blocked()) {
        return $user->is_mod();
      }
      return true;
    }

    /**
     * Check if user can delete another user's profile.
     * Only a profile owner and admins can delete it.
     * @param User $user User that deletes.
     * @param User $profile User to be deleted.
     * @return bool True if the profile can be deleted.
     */
    public function delete(User $user, User $profile)
    {
      return $user->user_id == $profile->user_id || $user->is_admin;
    }


    /**
     * Check if user can edit another user's profile.
     * Only a profile owner and admins can edit it.
     * @param User $user User that deletes.
     * @param User $profile User to be deleted.
     * @return bool True if the profile can be edit.
     */
    public function edit(User $user, User $profile)
    {
      return $user->user_id === $profile->user_id || $user->is_admin;
    }
}
