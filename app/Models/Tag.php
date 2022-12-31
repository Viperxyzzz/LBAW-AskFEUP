<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tag extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tag';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'tag_id';

    /**
     * Perform an exact search match for tag names.
     */
    public static function search(string $query, array $topics) {
      if (empty($topics)) return Tag::where('tag_name', 'like', "%$query%")->get();
      return Tag::where('tag_name', 'like', "%$query%")->whereIn('topic_id',$topics)->get();
    }

    /**
     * Get the topic the tag is currently associated with.
     */
    public function topic() {
      return $this->hasOne(Topic::class, 'topic_id', 'topic_id');
    }

}
