<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\QuestionVotes;

class Question extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'question';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'question_id';

    /**
     * The tags related to the question.
     */
    public function tags() {
        return $this->hasManyThrough(
            Tag::class,
            QuestionTag::class,
            'question_id',
            'tag_id',
            'question_id',
            'tag_id'
        );
    }

    /**
     * Get the author of this question
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author() {
        return $this->hasOne(User::class, 'user_id', 'author_id');
    }

    /**
     * Get all the answers to this question
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'question_id');
    }

    /**
     * Get all the comments to this question.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'question_id', 'question_id');
    }

    /**
     * Get all the comments directly on the question.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function question_comments()
    {
        return $this->comments()->select("*")
        ->whereNull('answer_id')
        ->get();
    }

    /**
     * Get the formatted date distance of the question.
     * @return string Formatted date string.
     */
    public function date_distance() {
        return Carbon::parse($this->date)->diffForHumans();
    }


    /**
     */
    public function __construct() {
    }

    /**
     * Check if a user's profile is accessible.
     * @return bool True is the user's profile is accessible, false otherwise (e.g. was deleted).
     */
    public function is_accessible_user(){
        $author = User::find($this->author_id);
        if (Auth::user()!=null && Auth::user()->is_admin)
            return true;
        if ($author->is_disable())
            return false;
        return true;
    }

    /**
     * Get the vote status of the logged user and this question.
     * @return mixed Returns the object of the vote.
     */
    public function vote(){
        $questionVote = null;
        if(Auth::user()!=null){        
        $questionVote = QuestionVotes::where('question_id', $this->question_id)
        ->where('user_id', Auth::user()->user_id)
        ->first();
        }
        if ($questionVote != null)
            return $questionVote->value;
        return $questionVote;
    }
}
