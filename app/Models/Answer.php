<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\AnswerVotes;

class Answer extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'answer';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'answer_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_text', 'num_votes', 'is_correct', 'was_edited', 'date', 'question_id', 'user_id'
    ];

    public function question(){
        return $this->hasOne(Question::class, 'question_id', 'question_id');
    }

    public function author() {
        return $this->hasOne(User::class, 'user_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'answer_id', 'answer_id');
    }

    public function is_accessible_user(){
        $author = User::find($this->user_id);
        if (Auth::user()!=null && Auth::user()->is_admin)
            return true;
        if ($author->is_disable())
            return false;
        return true;
    }
    public function vote(){
        $answerVote = AnswerVotes::where('answer_id', $this->answer_id)
        ->where('user_id', Auth::user()->user_id)
        ->first();
        if ($answerVote != null)
            return $answerVote->value;
        return $answerVote;
    }
}
