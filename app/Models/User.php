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
      return User::where('username', 'ilike', "%$query%")
        ->orWhere('name', 'ilike', "%$query%")->orderBy('username')->get();
    }

    /**
     * Get the number of answers the user has created.
     * @return int Number of answers.
     */
    public function get_n_answered()
    {
      $num_answered_questions = DB::table('answer')->where('user_id', $this->user_id)->count();
      return $num_answered_questions;
    }

    /**
     * Get the number of questions the user has asked.
     * @return int Total number of questions.
     */
    public function get_n_asked()
    {
      $num_asked_questions = DB::table('question')->where('author_id', $this->user_id)->count();
      return $num_asked_questions;
    }

    /**
     * Get the number of badges the user has been awarded.
     * @return int Total number of bages.
     */
    public function get_n_badges()
    {
      $num_badges = DB::table('user_badge')->where('user_id', $this->user_id)->count();
      return $num_badges;
    }

    /**
     * Get the number of tags the user is following.
     * @return int Total number of following tags.
     */
    public function get_n_ftags()
    {
      $num_ftags = DB::table('user_tag')->where('user_id', $this->user_id)->count();
      return $num_ftags;
    }

    /**
     * Get the titles of the last 3 asked questions.
     * @return \Illuminate\Support\Collection
     */
    public function get_last3_asked()
    {
      $titles3 = DB::table('question')->where('author_id', $this->user_id)
      ->orderby('question_id', 'DESC')
      ->limit(3)
      ->pluck('title');
      return $titles3;
    }

    /**
     * Get the last 3 received badges.
     * @return \Illuminate\Support\Collection
     */
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
     * Get all of the question this user has asked.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'author_id', 'user_id');
    }

    /**
     * Get all the questions the user is following.
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
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

    /**
     * Get all the tags the user if following.
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function tags_following()
    {
        return $this->hasManyThrough(
          Tag::class,
          UserTag::class,
          'user_id',
          'tag_id',
          'user_id',
          'tag_id'
        );
    }

    /**
     * Check if a user if following a tag.
     * @param mixed $tag_id The id of the tag to be checked.
     * @return mixed
     */
    public function follows_tag($tag_id) {
      return UserTag::where([
        ['user_id', '=', $this->user_id],
        ['tag_id', '=', $tag_id]
      ])->exists();
    }

    /**
     * Determine if a user is following a question
     * 
     * @param mixed Id of the question to check
     * @return Boolean True if user follows the question, false otherwise.
     */
    public function follows_question($question_id) {
      return QuestionUser::where([
        ['user_id', '=', $this->user_id],
        ['question_id', '=', $question_id]
      ])->exists();
    }

    /**
     * Get all the answers the user has created.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(Answer::class, 'user_id', 'user_id');
    }

    /**
     * Get all the comments the user has created.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'user_id');
    }

    /**
     * Check if a user is a moderator or above (admin). 
     * 
     * @return True if the user is either moderator or admin, false otherwise.
     */
    public function is_mod() {
      return ($this->is_admin || $this->is_moderator);
    }

    /**
     * Check if a user is an admin. 
     * 
     * @return True if the user is an admin, false otherwise.
     */
    public function is_admin() {
      return ($this->is_admin);
    }

    /**
     * Check if a user is blocked.
     * 
     * @return True if the user is blocked, false otherwise.
     */
    public function is_blocked() {
      return $this->hasOne(Block::class, 'user_id', 'user_id')->exists();
    }

    /**
     * Check if a user is disabled.
     * 
     * @return True if the user is disabled, false otherwise.
     */
    public function is_disable() {
      return $this->hasOne(Disable::class, 'user_id', 'user_id')->exists();
    }
    
    /**
     * Get all the notifications of this user.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications(){
      return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    /**
     * Get the number of non viewed notification.
     * @return int Total number of unread notifications.
     */
    public function num_non_viewed_notifications(){
      $num = DB::table('notification')
                    ->where([['viewed', 'No'], ['user_id', $this->user_id]])
                    ->count();
      return $num;
    }

    /**
     * Get all the badges from the user
     * @return mixed Collection of Badge model.
     */
    public function badges() {
        return $this->hasMany(
          UserBadge::class,
          'user_id',
          'user_id',
        )->orderBy('badge_id')->get();
    }

    /**
     * Check if a user is supporting a badge
     * @param mixed $badge_id Badge id to check.
     * @param mixed $user_id User that achieved the badge
     * @return Boolean True if the user supports the badge, false otherwise.
     */
    public function supports_badge($badge_id, $achiever_id) {
      return UserBadgeSupport::where(
        [
          ['user_who_supports', $this->user_id],
          ['user_who_achieves', $achiever_id],
          ['badge_id', $badge_id]
        ]
      )->exists();
    }
}
