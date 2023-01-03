<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Answer;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;

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
      // Only an answer's and admin author can delete it
      return $user->user_id == $answer->user_id || $user->is_admin;
    }
    public function edit(User $user, Answer $answer)
    {
      // Only an answer's and admin author can edit it
      return $user->user_id == $answer->user_id || $user->is_admin;
    }

    public function update(User $user, Answer $answer)
    {
      // Only an answer's author and admin can update it
      return $user->user_id == $answer->user_id || $user->is_admin;
    }

    public function valid(User $user, Answer $answer)
    {
      // Only an answer's author and admin can mark it as valid
      $question = Question::find($answer->question_id);
      return $user->user_id == $question->author_id || $user->is_admin;
    }

    public function vote(User $user, Answer $answer)
    {
      // Any user can vote on a answer
      return Auth::check();
    }
}
