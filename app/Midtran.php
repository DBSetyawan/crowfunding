<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Midtran extends Model
{
    protected $fillable = [
        'added_by_user_id',
        'group_id',
        'id_cabang',
    ];

    protected $dates = [
        'created_at',
    ];
    
    public function donaturs()
    {
        return $this->hasMany('App\Donatur', 'donatur_id');
    }

    public function donatursFK()
    {
        return $this->belongsTo('App\Donatur', 'donatur_id');
    }

    public function usersDonatur()
    {
        return $this->hasMany('App\User', 'id');
    }
    
}
