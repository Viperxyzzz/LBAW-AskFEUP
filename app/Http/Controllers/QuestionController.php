<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Question;
use App\Models\QuestionTag;
use App\Models\QuestionUser;
use App\Models\Tag;
use App\Models\Answer;
use App\Models\QuestionVotes;

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
      $question_comments = $question->question_comments();
      return view('pages.question', ['question' => $question,'answers' => $answers, 'comments' => $comments, 'question_comments' => $question_comments]);
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

    public function update(Request $request, $id)
    {
      if(!Auth::check()) return redirect('/login');
    
      $request->validate([
        'title' => 'required',
        'full_text' => 'required',
      ]);
      
      $question = Question::find($id);
      $this->authorize('edit', $question);
      $question->title = $request->get('title');
      $question->full_text = $request->full_text;

      $question->date = date('Y-m-d H:i:s');
      $question->was_edited = true;

      $question->save();

      // Delete all question tags
      $question_tags = QuestionTag::where('question_id', $question->question_id)->get();
      foreach($question_tags as $question_tag){
        $question_tag->delete();
      }

      // Add new question tags
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
      $this->authorize('edit', $question);
      $tags = Tag::all();
      return view('pages.edit_question',['tags' => $tags, 'question' => $question]);
    }

    public function vote(Request $request)
    {
      if(!Auth::check()) return redirect('/login');
      $question = Question::find($request->question_id);
      $this->authorize('vote', $question);
      //create a question vote
      //verify if user already voted
      $question_vote = QuestionVotes::where('question_id', $request->question_id)->where('user_id', Auth::user()->user_id)->first();
      //se o user ja votou
      if($question_vote !== null){
        if($question_vote->value == $request->value)
          return ['num_votes' => $question->num_votes, 'question_id' => $request->question_id];
        $question->num_votes -= $question_vote->value;
        $question->num_votes += $request->vote;
        $question->save();
        $question_vote->value = $request->vote;
        $question_vote->save();
        return ['num_votes' => $question->num_votes, 'question_id' => $request->question_id];
      }
      $question_vote = new QuestionVotes;
      $question_vote->question_id = $request->question_id;
      $question_vote->user_id = Auth::user()->user_id;
      $question_vote->value = $request->vote;
      $question_vote->save();
      $question->num_votes += $request->vote;
      $question->save();
      return ['num_votes' => $question->num_votes, 'question_id' => $request->question_id];;
    }

    public function create_view()
    {
      if (!Auth::check()) return redirect('/login');
      $tags = Tag::all();
      return view('pages.create_question',['tags' => $tags]);
    }

    /**
     * Follow a question
     * @param Request $request 
     * @param mixed $question_id Question id to be followed.
     * @return QuestionUser Returns JSON object of the new relation.
     */
    public function follow(Request $request, $question_id) {
        if (!Auth::check()) redirect('/login');
        if ($question_id == NULL) return;
        return QuestionUser::follow(Auth::id(), $question_id);
    }


    /**
     * Un-Follow a question
     * @param Request $request 
     * @param mixed $question_id Question id to be un-followed.
     * @return QuestionUser Returns JSON object of the deleted relation.
     */
    public function unFollow(Request $request, $question_id) {
        $follow = QuestionUser::where([
            ['user_id', '=', Auth::id()],
            ['question_id', '=', $question_id]
        ]);
        $follow->delete();
        return ['question_id' => $question_id];
    }
}
