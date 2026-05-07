<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NFLSchedule extends Model
{
    protected $table = 'nfl_schedule';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'year',
        'team',
        'week',
        'day',
        'date',
        'local_time',
        'est_time',
        'location',
    ];
}
