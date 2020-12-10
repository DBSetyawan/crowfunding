<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Midtran extends Model
{
    protected $guarded = [];  

    public function donaturs()
    {
        return $this->hasMany('App\Donatur', 'donatur_id');
    }

    public function usersDonatur()
    {
        return $this->hasMany('App\User', 'id');
    }
    
}
