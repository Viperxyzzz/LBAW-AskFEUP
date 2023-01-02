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

      $request->validate([
        'answer' => 'required|string|max:500'
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
      return json_encode($answer);
    }

    public function delete(Request $request, $answer_id) {
      if (!Auth::check()) return redirect('/login');
      $answer = Answer::find($answer_id);
      $this->authorize('delete', $answer);

      $answer->delete();
      return $answer;
    }

    public function edit_view(Request $request, $answer_id) {
      if (!Auth::check()) return redirect('/login');
      $answer = Answer::find($answer_id);
      $this->authorize('edit', $answer);

      return $answer;
    }

    public function update(Request $request, $answer_id) {
      if (!Auth::check()) return redirect('/login');
      $answer = Answer::find($answer_id);
      $this->authorize('update', $answer);

      $request->validate([
        'full_text' => 'required|string|max:500'
      ]);

      $answer->full_text = $request->input('full_text');

      $answer->date = date('Y-m-d H:i:s');
      $answer->was_edited = true;

      $answer->save();
      return $answer;
    }
}
