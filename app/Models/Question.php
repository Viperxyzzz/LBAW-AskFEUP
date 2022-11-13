<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;
    protected $table = 'question';
    protected $primaryKey = 'question_id';
}
