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
      $comment->full_text = $request->full_text;
      $comment->num_votes = 0;
      $comment->question_id = $request->question_id;
      $comment->date = date('Y-m-d H:i:s');
      $comment->answer_id = $request->answer_id;
      $comment->user_id = auth::id();
      $comment->save();

      $comment['author']['name'] = Auth::user()->name;
      $comment['author']['picture_path'] = Auth::user()->picture_path;
      $comment['date'] = date("d/m/Y");
      return json_encode($comment);
    }
}
