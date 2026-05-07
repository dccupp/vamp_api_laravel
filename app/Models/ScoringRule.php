<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoringRule extends Model
{
    protected $table = 'scoring_rules';
    protected $primaryKey = 'league_id'; // one record per league, league_id is the PK
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'league_id',
        'passing_yards',
        'passing_touchdowns',
        'interceptions_thrown',
        'two_point_pass',
        'passing_300_399',
        'passing_400_plus',
        'rushing_yards',
        'rushing_touchdowns',
        'two_point_rush',
        'rushing_100_199',
        'rushing_200_plus',
        'receiving_yards',
        'receptions',
        'receiving_touchdowns',
        'two_point_reception',
        'receiving_100_199',
        'receiving_200_plus',
        'kickoff_return_touchdown',
        'punt_return_touchdown',
        'fumble_recovered_touchdown',
        'fumbles_lost',
        'interception_return_touchdown',
        'fumble_return_touchdown',
        'blocked_return_touchdown',
        'two_point_return',
        'one_point_safety',
    ];
}
