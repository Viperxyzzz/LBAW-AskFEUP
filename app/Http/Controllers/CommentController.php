<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Comment;
use App\Models\CommentVotes;

class CommentController extends Controller
{

    /**
     * Create a comment and store it.
     * @param Request $request
     * @return mixed Returns the created comment in JSON format.
     */
    public function create(Request $request) {
      if (!Auth::check()) return redirect('/login');
      $this->authorize('create', Comment::class);

      $comment = new Comment();
      $request->validate([
        'full_text' => 'required|string|max:1000'
      ]);

      $comment->full_text = $request->input('full_text');
      $comment->num_votes = 0;
      $comment->question_id = $request->input('question_id');
      $comment->date = date('Y-m-d H:i:s');
      $comment->answer_id = $request->input('answer_id');
      $comment->user_id = auth::id();
      $comment->was_edited = false;
      $comment->save();

      $comment['author']['name'] = Auth::user()->name;
      $comment['author']['picture_path'] = Auth::user()->picture_path;
      $comment['date'] = date("d/m/Y");
      return json_encode($comment);
    }

    /**
     * Delete a comment from storage.
     * @param Request $request
     * @param mixed $comment_id The id of the comment ot be deleted.
     * @return mixed Returns the deleted's comment JSON object.
     */
    public function delete(Request $request, $comment_id) {
      if (!Auth::check()) return redirect('/login');
      $comment = Comment::find($comment_id);
      $this->authorize('delete', $comment);

      $comment->delete();
      return $comment;
    }

    /**
     * Get the comment to be edited. This is used to retrieve info before editing.
     * @param Request $request
     * @param mixed $comment_id Id of the comment to be retrieved.
     * @return mixed JSON object of the comment.
     */
    public function edit_view(Request $request, $comment_id) {
      if (!Auth::check()) return redirect('/login');
      $comment = Comment::find($comment_id);
      $this->authorize('edit', $comment);

      return $comment;
    }

    /**
     * Update a comment in storage.
     * @param Request $request
     * @param mixed $comment_id The id of the comment to be edited.
     * @return mixed JSON object of the comment.
     */
    public function update(Request $request, $comment_id) {
      if (!Auth::check()) return redirect('/login');
      $comment = Comment::find($comment_id);

      $this->authorize('update', $comment);

      $request->validate([
        'full_text' => 'required|string|max:1000'
      ]);

      $comment->full_text = $request->input('full_text');
      $comment->was_edited = true;
      $comment->date = date('Y-m-d H:i:s');

      $comment->save();
      return $comment;
    }

    /**
     * Create a vote on a comment
     * @param Request $request
     * @param mixed $comment_id The id of the comment to be edited.
     * @return mixed JSON object of the comment.
     */
    public function vote(Request $request, $comment_id) {
      if(!Auth::check()) return redirect('/login');

      $comment = Comment::find($request->comment_id);
      $this->authorize('vote', $comment);

      $commentVote = CommentVotes::where('comment_id', $request->comment_id)
        ->where('user_id', Auth::user()->user_id)
        ->first();

      if ($commentVote !== null) {
        // User has already voted
        if ($commentVote->value == $request->vote) {
          // User is trying to cancel their vote
          if ($comment->num_votes > 0 || $request->vote != -1) {
            // Only decrement the num_votes if it is above 0 or if the user is not downvoting
            $comment->num_votes -= $request->vote;
          }
          $commentVote->delete();
        } else {
          // User is updating their vote
          if($comment->num_votes != 0 || $commentVote->value != -1)
            $comment->num_votes -= $commentVote->value;
          $comment->num_votes += $request->vote;
          $commentVote->value = $request->vote;
          $commentVote->save();
        }
      } else {
        // User is casting a new vote
        $commentVote = new CommentVotes;
        $commentVote->comment_id = $request->comment_id;
        $commentVote->user_id = Auth::user()->user_id;
        $commentVote->value = $request->vote;
        $commentVote->save();
        $comment->num_votes += $request->vote;
      }
      if($comment->num_votes < 0) $comment->num_votes = 0;
      $comment->save();
      return ['num_votes' => $comment->num_votes, 'comment_id' => $request->comment_id, 'vote' => $request->vote];
    }
}
