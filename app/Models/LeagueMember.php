<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeagueMember extends Model
{
    protected $table = 'league_members';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'league_id',
        'user_id',
        'role',
        'team_name',
        'remaining_faab_budget',
        'is_vamp',
    ];

    public function league()
    {
        return $this->belongsTo(League::class, 'league_id', 'league_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
