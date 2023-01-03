<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Models\Question;
use App\Models\QuestionTag;
use App\Models\QuestionUser;
use App\Models\Tag;
use App\Models\QuestionVotes;

class QuestionController extends Controller
{
    /**
     * Show the page of a question.
     * @param mixed $question_id Id of the question to be displayed.
     * @return mixed Detailed question page.
     */
    public function home($question_id)
    {
      if(!Auth::check()) return redirect('/login');
      $question = Question::find($question_id);
      $answers = $question->answers();
      $comments = $question->comments();
      $question_comments = $question->question_comments();
      return view('pages.question', ['question' => $question,'answers' => $answers, 'comments' => $comments, 'question_comments' => $question_comments]);
    }

    /**
     * Create a new question
     * @param Request $request POST request to create question.
     * @return mixed After the question is created, redirects to question page.
     */
    public function create(Request $request)
    {
      if(!Auth::check()) return redirect('/login');
      $question = new Question;

      $request->validate([
        'title' => 'required|string|max:100',
        'full_text' => 'required|string|max:1000',
      ]);

      $question->title = $request->input('title');
      $question->full_text = $request->input('full_text');
      $question->author_id = Auth::user()->user_id;

      $question->num_votes = 0;
      $question->num_views = 0;
      $question->num_answers = 0;

      $question->date = date('Y-m-d H:i:s');

      $question->save();

      $tags = $request->tags;
      if($tags === null)
        return redirect('/question/'.$question->question_id)->with('message', 'Created question successfully!');
      for($i = 0; $i < count($tags); $i++){
        $question_tag = new QuestionTag;
        $question_tag->question_id = $question->question_id;
        $question_tag->tag_id = $tags[$i];
        $question_tag->save();
      }

      return redirect('/question/'.$question->question_id)->with('message', 'Created question successfully!');
    }

    /**
     * Edit a question
     * @param Request $request POST request with correct question details.
     * @param mixed $id Id of the question to be edited.
     * @return mixed After the question is edited, redirects to question page.
     */
    public function update(Request $request, $id)
    {
      if(!Auth::check()) return redirect('/login');
    
      $request->validate([
        'title' => 'required|string|max:100',
        'full_text' => 'required|string|max:1000',
      ]);
      
      $question = Question::find($id);
      $this->authorize('edit', $question);
      $question->title = $request->input('title');
      $question->full_text = $request->input('full_text');

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

      return redirect('/question/'.$question->question_id)->with('message', 'Changed question successfully!');
    }

    /**
     * Delete a question from storage.
     * @param Request $request
     * @param mixed After a question is deleted, redirects to the feed.
     */
    public function delete(Request $request)
    {
      if(!Auth::check()) return redirect('/login');
      $question = Question::find($request->question_id);
      $this->authorize('delete', $question);
      $question->delete();
      return redirect('/feed')->with('message', 'Deleted question successfully!');
    }

    /**
     * Get info of question to be edited.
     * @param Request $request Request including question id in the data.
     * @return mixed JSON object of a question.
     */
    public function edit_view(Request $request)
    {
      if (!Auth::check()) return redirect('/login');
      $question = Question::find($request->question_id);
      $this->authorize('edit', $question);
      $tags = Tag::all();
      return view('pages.edit_question',['tags' => $tags, 'question' => $question]);
    }

    /**
     * Create a vote on a question
     * @param Request $request
     * @return mixed JSON with number of votes and the id of the question.
     */
    public function vote(Request $request){
      if(!Auth::check()) return redirect('/login');

      $question = Question::find($request->question_id);
      $this->authorize('vote', $question);

      $questionVote = QuestionVotes::where('question_id', $request->question_id)
        ->where('user_id', Auth::user()->user_id)
        ->first();

      if ($questionVote !== null) {
        // User has already voted
        if ($questionVote->value == $request->vote) {
          // User is trying to cancel their vote
          if ($question->num_votes > 0 || $request->vote != -1) {
            // Only decrement the num_votes if it is above 0 or if the user is not downvoting
            $question->num_votes -= $request->vote;
          }
          $questionVote->delete();
        } else {
          // User is updating their vote
          if($question->num_votes != 0 || $questionVote->value != -1)
            $question->num_votes -= $questionVote->value;
          $question->num_votes += $request->vote;
          $questionVote->value = $request->vote;
          $questionVote->save();
        }
      } else {
        // User is casting a new vote
        $questionVote = new QuestionVotes;
        $questionVote->question_id = $request->question_id;
        $questionVote->user_id = Auth::user()->user_id;
        $questionVote->value = $request->vote;
        $questionVote->save();
        $question->num_votes += $request->vote;
      }
      if($question->num_votes < 0) $question->num_votes = 0;
      $question->save();
      return ['num_votes' => $question->num_votes, 'question_id' => $request->question_id, 'vote' => $request->vote];
    }

    /**
     * Get the view of create question.
     * @return mixed Returns the page used to create questions.
     */
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
