<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Kecamatan extends Model
{
    protected $table = 'tbl_kecamatan';
    protected $primaryKey = 'id';

    public function kabkot()
    {
        return $this->belongsTo('App\Kabkot','kabkot_id');
    }

}
