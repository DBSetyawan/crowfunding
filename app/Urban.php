<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Urban extends Model
{
    protected $table = 'db_postal_code_data';
    protected $primaryKey = 'id';

    public function province()
    {
        return $this->belongsTo('App\Province','province_code');
    }
}
