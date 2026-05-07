<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RosteredPlayerWeeklyScore extends Model
{
    protected $table = 'rostered_player_weekly_scores';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'league_id',
        'league_member_id',
        'rostered_player_id',
        'year',
        'week',
        'roster_position',
        'fantasy_points',
    ];
}
