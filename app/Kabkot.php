<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Kabkot extends Model
{
    protected $table = 'tbl_kabkot';
    protected $primaryKey = 'id';


    public function provinsi()
    {
        return $this->belongsTo('App\Provinsi','provinsi_id');
    }

}
