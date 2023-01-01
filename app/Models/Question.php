<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

    public function author() {
        return $this->hasOne(User::class, 'user_id', 'author_id');
    }


    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'question_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'question_id', 'question_id');
    }

    public function question_comments()
    {
        return $this->comments()->select("*")
        ->whereNull('answer_id')
        ->get();
    }

    public function date_distance() {
        return Carbon::parse($this->date)->diffForHumans();
    }

    public function is_accessible_user(){
        $author = User::find($this->author_id);
        if (Auth::user()->is_admin)
            return true;
        if ($author->is_disable())
            return false;
        return true;
    }


    /**
     */
    public function __construct() {
    }
}
