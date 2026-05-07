<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyStat extends Model
{
    protected $table = 'weekly_stats';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'season',
        'week',
        'passing_yards',
        'passing_tds',
        'interceptions',
        'two_point_passes',
        'rushing_yards',
        'rushing_tds',
        'two_point_rushes',
        'receptions',
        'receiving_yards',
        'receiving_tds',
        'two_point_receptions',
        'special_teams_tds',
    ];
}
