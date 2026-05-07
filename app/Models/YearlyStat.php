<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YearlyStat extends Model
{
    protected $table = 'yearly_stats';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'season',
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
