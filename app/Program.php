<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Program extends Model
{

    protected $fillable = [    	
        'program_name',
        'start_date',
        'end_date',
        'target_amount',
        'program_category_id',
        'description',
        'thumbnail',
        'type',
    ];

    
    public function program_category(){
        return $this->belongsTo('App\ProgramCategory','program_category_id');
    }
}
