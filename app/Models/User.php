<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function get_n_answered()
    {
      if (!Auth::check()) return redirect('/login');
      $user = $this->find(Auth::id());
      $num_answered_questions = DB::table('answer')->where('user_id', $user->user_id)->count();
      return $num_answered_questions;
    }

    public function get_n_asked()
    {
      if (!Auth::check()) return redirect('/login');
      $user = $this->find(Auth::id());
      $num_asked_questions = DB::table('question')->where('author_id', $user->user_id)->count();
      return $num_asked_questions;
    }

    public function get_n_badges()
    {
      if (!Auth::check()) return redirect('/login');
      $user = $this->find(Auth::id());
      $num_badges = DB::table('user_badge')->where('user_id', $user->user_id)->count();
      return $num_badges;
    }

    public function get_n_ftags()
    {
      if (!Auth::check()) return redirect('/login');
      $user = $this->find(Auth::id());
      $num_ftags = DB::table('user_tag')->where('user_id', $user->user_id)->count();
      return $num_ftags;
    }

    public function get_last3_asked()
    {
      if (!Auth::check()) return redirect('/login');
      $user = $this->find(Auth::id());
      $titles3 = DB::table('question')->where('author_id', $user->user_id)
      ->orderby('question_id', 'DESC')
      ->limit(3)
      ->pluck('title');
      return $titles3;
    }

    public function get_last3_badges()
    {
      if (!Auth::check()) return redirect('/login');
      $user = $this->find(Auth::id());
      $badge_names3 = DB::table('badge')
                    ->rightJoin('user_badge', 'user_badge.badge_id', '=', 'badge.badge_id')
                    ->where('user_id', $user->user_id)
                    ->orderby('badge.badge_id', 'DESC')
                    ->limit(3)
                    ->pluck('badge.badge_name');
      return $badge_names3;
    }

}
