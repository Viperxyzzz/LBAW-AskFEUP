<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'report_id';

    /**
     * Get a string that describes the type of content (either question, answer or comment) and it's id.
     * 
     * @return string String of the type Comment #475
     */
    public function type() {
        if (!is_null($this->answer_id)) {
            return "Answer #$this->answer_id";
        }
        else if (!is_null($this->comment_id)) {
            return "Comment #$this->comment_id";
        }
        
        return "Question #$this->question_id";
    }

    /**
     * Get the url to the reported content (either question, answer or comment)
     * 
     * @return string url of the content in question.
     */
    public function url() {
        if (!is_null($this->answer_id)) {
            return url("question/$this->question_id#answer_$this->answer_id");
        }
        else if (!is_null($this->comment_id)) {
            return url("question/$this->question_id#comment_$this->comment_id");
        }
        
        return url("question/$this->question_id");
    }

    /**
     * Get the text that caused the report (either question, answer or comment)
     * 
     * @return string Text that caused the report
     */
    public function content() {
        if (!is_null($this->answer_id)) {
            return $this->hasOne(Answer::class, 'answer_id', 'answer_id')->first()->full_text;
        }
        else if (!is_null($this->comment_id)) {
            return $this->hasOne(Comment::class, 'comment_id', 'comment_id')->first()->full_text;
        }
        
        
        return $this->hasOne(Question::class, 'question_id', 'question_id')->first()->title;
        
    }

    /**
     * Get the author of the reported content.
     * 
     * @return User User that created the reported content.
     */
    public function author() {
        if (!is_null($this->answer_id)) {
            return $this->hasOne(Answer::class, 'answer_id', 'answer_id')->first()->author()->first();
        }
        else if (!is_null($this->comment_id)) {
            return $this->hasOne(Comment::class, 'comment_id', 'comment_id')->first()->author()->first();
        }
        
        return $this->hasOne(Question::class, 'question_id', 'question_id')->first()->author()->first();
    }

}
