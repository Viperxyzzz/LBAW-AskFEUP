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

    /**
     * Check which users can create answers.
     * Any logged user can create a new answer
     * @param User $user User to check.
     * @return bool True if the user can create answers.
     */
    public function create(User $user)
    {
      return Auth::check();
    }

    /**
     * Check which users can delete the answer.
     * Only an answer's author and admins can delete it
     * @param User $user User to check.
     * @param Answer answer to check.
     * @return bool True if the user can delete the answer.
     */
    public function delete(User $user, Answer $answer)
    {
      return $user->user_id == $answer->user_id || $user->is_admin;
    }

    /**
     * Check which users can edit the answer.
     * Only an answer's author and admins can edit it
     * @param User $user User to check.
     * @param Answer answer to check.
     * @return bool True if the user can edit the answer.
     */
    public function edit(User $user, Answer $answer)
    {
      return $user->user_id == $answer->user_id || $user->is_admin;
    }

    /**
     * Check which users can edit the answer.
     * Only an answer's author and admins can edit it
     * @param User $user User to check.
     * @param Answer answer to check.
     * @return bool True if the user can edit the answer.
     */
    public function update(User $user, Answer $answer)
    {
      return $user->user_id == $answer->user_id || $user->is_admin;
    }


    /**
     * Check which users can validate the answer (mark as correct)
     * Only an answer's author and admins can validate it
     * @param User $user User to check.
     * @param Answer answer to check.
     * @return bool True if the user can validate the answer.
     */
    public function valid(User $user, Answer $answer)
    {
      // Only an answer's author and admin can mark it as valid
      $question = Question::find($answer->question_id);
      return $user->user_id == $question->author_id || $user->is_admin;
    }


    /**
     * Check which users can vote on the answer.
     * All logged users can vote.
     * @param User $user User to check.
     * @param Answer answer to check.
     * @return bool True if the user can vote on the answer.
     */
    public function vote(User $user, Answer $answer)
    {
      // Any user can vote on a answer
      return Auth::check();
    }
}
