<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class BlockPolicy
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
      return $user->is_admin();
    }

}
