<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Donatur extends Model
{
    // public $additional_attributes = ['user_full_name'];
    // public $incrementing = true;
    protected $fillable = [
        'id',
        'nama',
        'no_hp',
        'pekerjaan',
        'alamat',
        'kelurahan_id',
        'donatur_group_id',
        'added_by_user_id',
        'user_id'
    ];
    
    public function kelurahan()
    {
        return $this->belongsTo('App\Kelurahan', 'kelurahan_id');
    }

    public function getUserFullNameAttribute()
    {
        return "uwotm8bayonetkarambit";
    }


}
