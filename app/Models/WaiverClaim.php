<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaiverClaim extends Model
{
    protected $table = 'waiver_claims';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'league_id',
        'league_member_id',
        'player_id',
        'season',
        'week',
        'rostered_player_to_drop',
        'faab_claim_amount',
        'league_member_priority',
        'is_active',
        'last_modified',
    ];
}
