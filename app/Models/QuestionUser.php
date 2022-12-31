<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QuestionUser extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'question_user_follower';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Add a follow question entry to storage.
     * @param mixed $user_id User id that follows.
     * @param mixed $question_id Question id that is being followed
     * @return QuestionUser Returns QuestionUser created model
     */
    public static function follow($user_id, $question_id) {
        $follow = new QuestionUser;
        $follow->user_id = $user_id;
        $follow->question_id = $question_id;
        $follow->save();
        return $follow;
    }
}
