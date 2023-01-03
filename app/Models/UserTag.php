<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserTag extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_tag';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Follow a tag.
     * @param mixed $user_id User that is following the tag.
     * @param mixed $tag_id Id of the tag to be followed.
     * @return UserTag JSON of the newly created following relation.
     */
    public static function follow($user_id, $tag_id) {
        $follow = new UserTag;
        $follow->user_id = $user_id;
        $follow->tag_id = $tag_id;
        $follow->save();
        return $follow;
    }

}
