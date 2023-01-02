<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Question;
use App\Models\QuestionTag;
use App\Models\Tag;
use App\Models\Answer;
use App\Models\Comment;

class CommentController extends Controller
{

    /**
     * Post an comment to a question or answer.
     * 
     * @return TODO
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
    public function delete(Request $request, $comment_id) {
      if (!Auth::check()) return redirect('/login');
      $comment = Comment::find($comment_id);
      $this->authorize('delete', $comment);

      $comment->delete();
      return $comment;
    }
    public function edit_view(Request $request, $comment_id) {
      if (!Auth::check()) return redirect('/login');
      $comment = Comment::find($comment_id);
      $this->authorize('edit', $comment);

      return $comment;
    }

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
}
