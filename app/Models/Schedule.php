<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'league_id',
        'home_league_member',
        'away_league_member',
        'week',
        'year',
        'home_score',
        'away_score',
        'winner',
    ];
}
