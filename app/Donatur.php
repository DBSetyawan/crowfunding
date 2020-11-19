<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Donatur extends Model
{
    public $additional_attributes = ['user_full_name'];

    public function kelurahan()
    {
        return $this->belongsTo('App\Kelurahan', 'kelurahan_id');
    }

    public function getUserFullNameAttribute()
    {
        return "uwotm8bayonetkarambit";
    }


}
