<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\CommentVotes;

class Comment extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comment';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'comment_id';
    
    /**
     * Get the author of this comment.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author() {
        return $this->hasOne(User::class, 'user_id', 'user_id');
    }

    /**
     * Check if a user's profile is accessible.
     * @return bool True is the user's profile is accessible, false otherwise (e.g. was deleted).
     */
    public function is_accessible_user(){
        $author = User::find($this->user_id);
        if (Auth::user()!=null && Auth::user()->is_admin)
            return true;
        if ($author->is_disable())
            return false;
        return true;
    }

    /**
     * Get the vote status of the logged user and this comment.
     * @return mixed Returns the object of the vote.
     */
    public function vote(){
        $commentVote = null;
        if(Auth::user()!=null){        
        $commentVote = CommentVotes::where('comment_id', $this->comment_id)
        ->where('user_id', Auth::user()->user_id)
        ->first();
        }
        if ($commentVote != null)
            return $commentVote->value;
        return $commentVote;
    }
}
