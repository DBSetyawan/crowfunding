<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class DonaturGroup extends Model
{
    protected $table = 'donatur_groups';

    protected $fillable = [
        'id_cabang',
        'id_parent',
        'id',
        'id_petugas',
        'id_users',
        'donatur_group_name',
        // 'add_by_user_id'
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
    public function hasManyToUserRelationship()
    {
        return $this->hasMany(User::class, 'add_by_user_id');
    }

}
