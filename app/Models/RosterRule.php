<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RosterRule extends Model
{
    protected $table = 'roster_rules';
    protected $primaryKey = null; // identified by league_id + roster_type_id composite
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'league_id',
        'roster_type_id',
        'quarterback_count',
        'running_back_count',
        'wide_receiver_count',
        'tight_end_count',
        'wide_receiver_tight_end_count',
        'flex_count',
        'bench_count',
        'ir_count',
        'max_roster_size',
        'max_qb_count',
        'max_rb_count',
        'max_wr_count',
        'max_te_count',
        'beginning_faab',
    ];
}
