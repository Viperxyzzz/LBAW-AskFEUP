<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerVotes extends Model
{
    use HasFactory;
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'answer_votes';
    /**
     * The primary key associated with the table.
     * 
     * @var string
     */
    protected $primaryKey = 'answer_id';
}
