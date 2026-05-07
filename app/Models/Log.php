<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
  protected $table = 'logging';
  public $timestamps = false;

  protected $fillable = [
    'league_member_id',
    'type',
    'message',
  ];
}