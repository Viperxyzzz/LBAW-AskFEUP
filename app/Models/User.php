<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'username', 'name', 'email', 'password', 'score', 'is_moderator', 'is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Perform an exact search match for usernames.
     */
    public static function search(string $query) {
      return User::where('username', 'like', "%$query%")->orderBy('username')->get();
    }

    public function get_n_answered()
    {
      
      $num_answered_questions = DB::table('answer')->where('user_id', $this->user_id)->count();
      return $num_answered_questions;
    }

    public function get_n_asked()
    {
      $num_asked_questions = DB::table('question')->where('author_id', $this->user_id)->count();
      return $num_asked_questions;
    }

    public function get_n_badges()
    {
      $num_badges = DB::table('user_badge')->where('user_id', $this->user_id)->count();
      return $num_badges;
    }

    public function get_n_ftags()
    {
      $num_ftags = DB::table('user_tag')->where('user_id', $this->user_id)->count();
      return $num_ftags;
    }

    public function get_last3_asked()
    {
      $titles3 = DB::table('question')->where('author_id', $this->user_id)
      ->orderby('question_id', 'DESC')
      ->limit(3)
      ->pluck('title');
      return $titles3;
    }

    public function get_last3_badges()
    {
      $badge_names3 = DB::table('badge')
                    ->rightJoin('user_badge', 'user_badge.badge_id', '=', 'badge.badge_id')
                    ->where('user_id', $this->user_id)
                    ->orderby('badge.badge_id', 'DESC')
                    ->limit(3)
                    ->pluck('badge.badge_name');
      return $badge_names3;
    }
  
    /**
     * Get all of the comments for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'author_id', 'user_id');
    }

    public function questions_following()
    {
        return $this->hasManyThrough(
          Question::class,
          QuestionUser::class,
          'user_id',
          'question_id',
          'user_id',
          'question_id'
        );
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'user_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'user_id');
    }
}
