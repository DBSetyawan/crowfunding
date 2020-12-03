<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class DonaturGroup extends Model
{
    protected $fillable = [
        'id',
        'donatur_group_name'
    ];

    public function hasManyToUserRelationship()
    {
        return $this->hasMany('App\User', 'id');
    }

}
