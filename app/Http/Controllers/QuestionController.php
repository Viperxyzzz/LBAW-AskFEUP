<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Question;
use App\Models\QuestionTag;
use App\Models\Tag;
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
      //$this->authorize('list', Question::class);
      $question = Question::find($question_id);
      $answers = $question->answers();
      $comments = $question->comments();
      return view('pages.question', ['question' => $question,'answers' => $answers, 'comments' => $comments]);
    }

    public function create(Request $request)
    {
      if(!Auth::check()) return redirect('/login');
      $question = new Question;
      $question->title = $request->title;
      $question->full_text = $request->full_text;
      $question->author_id = Auth::user()->user_id;

      $question->num_votes = 0;
      $question->num_views = 0;
      $question->num_answers = 0;

      $question->date = date('Y-m-d H:i:s');

      $question->save();

      $tags = $request->tags;
      if($tags === null)
        return redirect('/question/'.$question->question_id);
      for($i = 0; $i < count($tags); $i++){
        $question_tag = new QuestionTag;
        $question_tag->question_id = $question->question_id;
        $question_tag->tag_id = $tags[$i];
        $question_tag->save();
      }

      return redirect('/question/'.$question->question_id);
    }

    public function update(Request $request)
    {
      if(!Auth::check()) return redirect('/login');
      $question = Question::find($request->question_id);
      $question->title = $request->title;
      $question->full_text = $request->full_text;

      $question->num_votes = $request->num_votes;
      $question->num_views = $request->num_views;
      $question->num_answers = $request->num_answers;

      $question->date = date('Y-m-d H:i:s');
      $question->was_edited = true;

      $question->save();

      $tags = $request->tags;
      if($tags === null)
        return redirect('/question/'.$question->question_id);
      for($i = 0; $i < count($tags); $i++){
        $question_tag = new QuestionTag;
        $question_tag->question_id = $question->question_id;
        $question_tag->tag_id = $tags[$i];
        $question_tag->save();
      }

      return redirect('/question/'.$question->question_id);
    }

    public function delete(Request $request)
    {
      if(!Auth::check()) return redirect('/login');
      $question = Question::find($request->question_id);
      $this->authorize('delete', $question);
      $question->delete();
      
      return redirect('/feed');
    }

    public function edit_view(Request $request)
    {
      if (!Auth::check()) return redirect('/login');
      $question = Question::find($request->question_id);
      $tags = Tag::all();
      return view('pages.edit_question',['tags' => $tags, 'question' => $question]);
    }

    public function create_view()
    {
      if (!Auth::check()) return redirect('/login');
      $tags = Tag::all();
      return view('pages.create_question',['tags' => $tags]);
    }

}
