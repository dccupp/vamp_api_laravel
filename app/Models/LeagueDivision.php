<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeagueDivision extends Model
{
    protected $table = 'league_divisions';
    protected $primaryKey = 'division_id';
    public $timestamps = false;

    protected $fillable = [
        'league_id',
        'name',
    ];
}
