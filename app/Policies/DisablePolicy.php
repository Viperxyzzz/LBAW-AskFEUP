<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class DisablePolicy
{
    use HandlesAuthorization;

    /**
     * Checks if a user can create a block
     * 
     * @param User User to check.
     * @return Bool True if the user is an admin, false otherwise.
     */
    public function create(User $user)
    {
      return $user->user_id == Auth()->user()->user_id || $user->is_admin;
    }

}