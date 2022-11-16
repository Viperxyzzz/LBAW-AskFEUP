<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Question;

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
    public function answer(array $data) {
      if (!Auth::check()) return redirect('/login');
      return Answer::create([
          'full_text' => $data['answer'],
          'num_votes' => 0,
          'is_correct' => False,
          'question_id' => $data['question_id'],
          'user_id' => Auth::id()
      ]);
    }
}
