<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
  use HasFactory;

  // Don't add create and update timestamps in database.
  public $timestamps = false;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'badge';

  /**
   * The primary key associated with the table.
   *
   * @var string
   */
  protected $primaryKey = 'badge_id';

}