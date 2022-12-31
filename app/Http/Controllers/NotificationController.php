<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

use App\Models\Notification;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Comment;

class NotificationController extends Controller
{
    public function update($notification_id) {
      if (!Auth::check()) return redirect('/login');
      $notification = Notification::find($notification_id);
      $notification->update(['viewed' => 'Yes']);
      return $notification;
    }

    public function redirectNotification($notification_id){
      $notification = Notification::find($notification_id);
      switch($notification->event_type){
          case 'new answer':
            $answer = Answer::find($notification->event_id);
            return redirect('/question/'.$answer->question_id.'/#answer_'.$notification->event_id);
          
          case 'new vote':
            $comment = Comment::find($notification->event_id);
            return redirect('/question/'.$comment->question_id.'/#comment_'.$notification->event_id);
  
          case 'correct answer':
            $answer = Answer::find($notification->event_id);
            return redirect('/question/'.$answer->question_id.'/#answer_'.$notification->event_id);
  
          case 'new badge':
            $user_badge = DB::table('user_badge')->where([['badge_id', $notification->event_id], ['user_id', Auth::user()->user_id]])->get()[0];
            return redirect('/users/'.$user_badge->user_id);
          }
      }
}
