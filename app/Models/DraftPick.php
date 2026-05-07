<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftPick extends Model
{
    protected $table = 'draft_picks';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'temp_player_id',
        'player_name',
        'position',
        'team',
        'draft_year',
        'draft_round',
        'draft_pick',
        'gsis_id',
        'pfr_player_id',
    ];
}
