<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;
    protected $table = 'topic';
    protected $primaryKey = 'topic_id';
}
