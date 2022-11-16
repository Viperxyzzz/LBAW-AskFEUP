<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Question;
use App\Models\Answer;

class QuestionController extends Controller
{

    /**
     * Shows all questions.
     *
     * @return Response
     */
    public function home($question_id)
    {
      if (!Auth::check()) return redirect('/login');
      //$this->authorize('list', Question::class);
      $question = Question::find($question_id);
      $answers = $question->answers();
      $comments = $question->comments();
      return view('pages.question', ['question' => $question,'answers' => $answers, 'comments' => $comments]);
    }

    /**
     * Post an answer to a question.
     * 
     * @return TODO
     */
    public function answer(Request $request, $question_id) {
      //if (!Auth::check()) return redirect('/login');
      $answer = new Answer();
      $answer->answer_id = 302;
      $answer->full_text = $request->input('answer');
      $answer->num_votes = 0;
      $answer->is_correct = false;
      $answer->question_id = $question_id;
      $answer->user_id = auth::id();
      $answer->save();

      //return json_encode($answer);
      //return response()->json(json_encode($answer), 200);
      return $answer;
    }
}
