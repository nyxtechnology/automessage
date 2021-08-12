<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailSchedulings extends Model
{
    protected $fillable = ['id', 'external_id', 'from', 'from_name', 'to', 'to_name', 'subject', 'body', 'delivery_date', 'template_variables', 'sent', 'event_stop'];
    protected $casts = ['id' => 'string', ];
}
