<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Question;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class QuestionPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
      // Any user can create a new question
      return Auth::check();
    }

    public function edit(User $user, Question $question)
    {
      // Only the author of the question can edit it
      return $user->user_id == $question->author_id || $user->is_admin;
    }

    public function delete(User $user, Question $question)
    {
      // Only a question's author can delete it
      return $user->user_id == $question->author_id || $user->is_admin;
    }
}