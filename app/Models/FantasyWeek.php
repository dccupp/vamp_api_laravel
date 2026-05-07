<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FantasyWeek extends Model
{
    protected $table = 'fantasy_weeks';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'year',
        'week',
        'begin_datetime',
        'end_datetime',
    ];
}
