<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Question;
use App\Models\Answer;
use App\Models\AnswerVotes;

class AnswerController extends Controller
{
    /**
     * Post an answer to a question.
     * @param Request $request Request to answer with correct params.
     * @param mixed $question_id Question to be answered.
     * @return mixed JSON of answer created.
     */
    public function create(Request $request, $question_id) {
      if (!Auth::check()) return redirect('/login');
      $this->authorize('create', Answer::class);

      $request->validate([
        'answer' => 'required|string|max:1000'
      ]);

      $answer = new Answer();
      $answer->full_text = $request->input('answer');
      $answer->num_votes = 0;
      $answer->is_correct = false;
      $answer->question_id = $question_id;
      $answer->user_id = auth::id();
      $answer->save();

      $answer['author']['name'] = Auth::user()->name;
      $answer['author']['picture_path'] = Auth::user()->picture_path;
      $answer['date'] = date("d-m-Y");
      $answer['question_author_id'] = Question::find($question_id)->author_id;
      return json_encode($answer);
    }

    /**
     * Delete an answer
     * @param Request $request Request to delete answer
     * @param mixed $answer_id Id of the answer ot be deleted.
     * @return mixed JSON of the deleted answer.
     */
    public function delete(Request $request, $answer_id) {
      if (!Auth::check()) return redirect('/login');
      $answer = Answer::find($answer_id);
      $this->authorize('delete', $answer);

      $answer->delete();
      return $answer;
    }

    /**
     * Get info about an answer to be edited
     * @param Request $request GET request.
     * @param mixed $answer_id Id of the answer requested.
     * @return mixed JSON of requested answer.
     */
    public function edit_view(Request $request, $answer_id) {
      if (!Auth::check()) return redirect('/login');
      $answer = Answer::find($answer_id);
      $this->authorize('edit', $answer);

      return $answer;
    }

    /**
     * Update a question in storage.
     * @param Request $request Request with correct answer info.
     * @param mixed $answer_id Id of the answer to be edited.
     * @return mixed JSON of the updated answer.
     */
    public function update(Request $request, $answer_id) {
      if (!Auth::check()) return redirect('/login');
      $answer = Answer::find($answer_id);
      $this->authorize('update', $answer);

      $request->validate([
        'full_text' => 'required|string|max:1000'
      ]);

      $answer->full_text = $request->input('full_text');

      $answer->date = date('Y-m-d H:i:s');
      $answer->was_edited = true;

      $answer->save();
      return $answer;
    }

    /**
     * Select answer as valid (choose as correct).
     * @param Request $request GET request
     * @param mixed $answer_id Id of the answer to be checked.
     * @return mixed JSON of the new valid answer.
     */
    public function make_valid(Request $request, $answer_id) {
      if (!Auth::check()) return redirect('/login');
      $answer = Answer::find($answer_id);
      $this->authorize('valid', $answer);

      $answer->is_correct = true;
      $answer->save();
      return $answer;
    }

    /**
     * Remove is correct from answer.
     * @param Request $request Get request.
     * @param mixed $answer_id Id of the answer ot invalidate.
     * @return mixed JSON of the changed answer.
     */
    public function make_invalid(Request $request, $answer_id) {
      if (!Auth::check()) return redirect('/login');
      $answer = Answer::find($answer_id);
      $this->authorize('valid', $answer);

      $answer->is_correct = false;
      $answer->save();
      return $answer;
    }

    /**
     * Add a vote to a question.
     * @param Request $request
     * @return mixed JSON with number of votes, answer id and the vote.
     */
    public function vote(Request $request){
      if(!Auth::check()) return redirect('/login');
      $answer = Answer::find($request->answer_id);
      $this->authorize('vote', $answer);
      $answerVote = AnswerVotes::where('answer_id', $request->answer_id)
        ->where('user_id', Auth::user()->user_id)
        ->first();

      if ($answerVote !== null) {
        // User has already voted
        if ($answerVote->value == $request->vote) {
          // User is trying to cancel their vote
          if ($answer->num_votes > 0 || $request->vote != -1){
            // Only decrement the num_votes if it is above 0 or if the user is not downvoting
            $answer->num_votes -= $request->vote;
          }
          $answerVote->delete();
        } else {
          // User is updating their vote
          if($answer->num_votes != 0 || $answerVote->value != -1)
            $answer->num_votes -= $answerVote->value;
          $answer->num_votes += $request->vote;
          $answerVote->value = $request->vote;
          $answerVote->save();
        }
      } else {
        // User is casting a new vote
        $answerVote = new AnswerVotes;
        $answerVote->answer_id = $request->answer_id;
        $answerVote->user_id = Auth::user()->user_id;
        $answerVote->value = $request->vote;
        $answerVote->save();
        $answer->num_votes += $request->vote;
      }
      if($answer->num_votes < 0) $answer->num_votes = 0;
      $answer->save();
      return ['num_votes' => $answer->num_votes, 'answer_id' => $request->answer_id, 'vote' => $request->vote];
    }
}
