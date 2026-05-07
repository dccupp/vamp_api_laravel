<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $table = 'leagues';
    protected $primaryKey = 'league_id';

    protected $fillable = [
        'name',
        'is_active',
    ];
}
