<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disable extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'disable_user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'disable_id';
    
     // Don't add create and update timestamps in database.
     public $timestamps  = false;

     /**
      * Get the disabled user.
      * 
      * @return User User model of the disabled user.
      */
     public function user() {
       return $this->hasOne(User::class, 'user_id', 'user_id')->first();
     }
 }