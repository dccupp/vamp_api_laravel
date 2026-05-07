<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RosteredPlayer extends Model
{
    protected $table = 'rostered_players';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'league_member_id',
        'player_id',
        'roster_position',
        'is_rostered',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}
