<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class DonaturGroup extends Model
{
    protected $fillable = [
        'donatur_group_name',
        'id',
        'id_petugas',
        'id_cabang',
        'id_parent',
        'id_users',
        'add_by_user_id'
    ];

    public function hasManyToUserRelationship()
    {
        return $this->hasMany(User::class, 'add_by_user_id');
    }

}
