<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Kelurahan extends Model
{
    protected $table = 'tbl_kelurahan';
    protected $primaryKey = 'id';
    
    public function kecamatan()
    {
        return $this->belongsTo('App\Kecamatan','kecamatan_id');
    }
}
