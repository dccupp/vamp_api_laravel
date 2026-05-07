<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaiverRule extends Model
{
    protected $table = 'waiver_rules';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'league_id',
        'waivers_length',
        'waiver_day',
    ];
}
