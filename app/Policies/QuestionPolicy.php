<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Question;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class QuestionPolicy
{
    use HandlesAuthorization;

    /**
     * Check if the user can create a question.
     * Every logged user can create a question.
     * @param User $user User to check
     * @return bool True if the user can create questions.
     */
    public function create(User $user)
    {
      return Auth::check();
    }

    /**
     * Check which users can edit the question.
     * Only a question's author and admins can edit it
     * @param User $user User to check.
     * @param Question question to check.
     * @return bool True if the user can edit the question.
     */
    public function edit(User $user, Question $question)
    {
      return $user->user_id == $question->author_id || $user->is_admin;
    }

    /**
     * Check which users can delete the question.
     * Only a question's author and admins can delete it
     * @param User $user User to check.
     * @param Question question to check.
     * @return bool True if the user can delete the question.
     */
    public function delete(User $user, Question $question)
    {
      return $user->user_id == $question->author_id || $user->is_admin;
    }

    /**
     * Check which users can vote the question.
     * All logged users can vote on questions.
     * @param User $user User to check.
     * @param Question question to check.
     * @return bool True if the user can vote on the question.
     */
    public function vote(User $user, Question $question)
    {
      return Auth::check();
    }
}