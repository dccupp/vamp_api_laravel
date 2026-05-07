<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RosterType extends Model
{
    protected $table = 'roster_types';
    protected $primaryKey = 'roster_type_id';
    public $timestamps = false;

    protected $fillable = [
        'type',
    ];
}
