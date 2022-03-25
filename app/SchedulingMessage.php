<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulingMessage extends Model
{
    use HasFactory;

    public $fillable = [
        'id',
        'classes',
        'conditions_stop',
        'conditions_update',
        'operation',
        'delivery_date',
        'processed'
    ];

    public $casts = ['id' => 'string', ];

    public $incrementing = false;
}
