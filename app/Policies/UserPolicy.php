<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    use HandlesAuthorization;

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
