<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Question;
use App\Models\QuestionTag;
use App\Models\Tag;
use App\Models\Answer;

class AnswerController extends Controller
{
    /**
     * Post an answer to a question.
     * 
     * @return TODO
     */
    public function create(Request $request, $question_id) {
      if (!Auth::check()) return redirect('/login');
      $this->authorize('create', Answer::class);
      $answer = new Answer();
      $answer->full_text = $request->input('answer');
      $answer->num_votes = 0;
      $answer->is_correct = false;
      $answer->question_id = $question_id;
      $answer->user_id = auth::id();
      $answer->save();

      $answer['author'] = Auth::user()->name;
      $answer['date'] = date("d-m-Y");
      return json_encode($answer);
    }

    public function delete(Request $request, $answer_id) {
      if (!Auth::check()) return redirect('/login');
      $answer = Answer::find($answer_id);
      $this->authorize('delete', $answer);

      $answer->delete();
      return back()->with("status", "Answer deleted successfully!");
    }
}
