<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulingMessage extends Model
{
    use HasFactory;

    public $fillable = [
        'id',
        'data',
        'conditions_stop',
        'conditions_update',
        'sent',
        'delivery_date'
    ];

    public $casts = ['id' => 'string', ];

    public $incrementing = false;
}
