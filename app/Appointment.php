<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $guarded = [];

    public function doctor() {
        return $this->belongsTo('App\User','user_id');
    }
}
