<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Answer;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class AnswerPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
      // Any user can create a new answer
      return Auth::check();
    }

    public function delete(User $user, Answer $answer)
    {
      // Only an answer's author can delete it
      return $user->user_id == $answer->user_id || $user->is_admin;
    }
    public function edit(User $user, Answer $answer)
    {
      // Only an answer's author can edit it
      return $user->user_id == $answer->user_id || $user->is_admin;
    }

    public function update(User $user, Answer $answer)
    {
      // Only an answer's author can update it
      return $user->user_id == $answer->user_id || $user->is_admin;
    }
}
