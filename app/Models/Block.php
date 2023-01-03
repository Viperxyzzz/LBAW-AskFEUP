<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blocks';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'block_id';

    /**
     * Get the blocked user.
     * 
     * @return User User model of the blocked user.
     */
    public function user() {
      return $this->hasOne(User::class, 'user_id', 'user_id')->first();
    }
}
