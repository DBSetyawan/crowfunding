<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Http\Controllers\VoyagerController as BaseVoyagerController;

class VoyagerController extends BaseVoyagerController
{
    //

    public function index()
    {

        if(Auth::user()->role_id == 1){
            return view('vendor.voyager.index');

        }elseif(Auth::user()->role_id == 2){
            dd("aaaaaa");
        }elseif(Auth::user()->role_id == 3){
            // return Redirect::action('IssuerController@index');
            dd("asda");
       
        }else{
    
          return  view('voyager::XXXXXXX.cardholder');
        }
    }
}
