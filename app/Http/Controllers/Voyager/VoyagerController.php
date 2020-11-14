<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Http\Controllers\VoyagerController as BaseVoyagerController;

class VoyagerController extends BaseVoyagerController
{
    //

    public function index()
    {
        // dd(auth()->user()->role->all());
        if(auth()->user()->role->all()){ //admin pusat
           
            return view('vendor.voyager.index');

        } else {
            return view('vendor.voyager.index');

        }
    }
}
