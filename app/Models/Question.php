<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        return $this->hasMany(Answer::class, 'question_id', 'question_id');
    }

    public function date_distance() {
        return Carbon::parse($this->date)->diffForHumans();
    }


    /**
     */
    public function __construct() {
    }
}
