<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Http\Controllers\VoyagerController as BaseVoyagerController;

class VoyagerController extends BaseVoyagerController
{
    //

    public function index()
    {
        // dd(Auth::user()->role_id);

        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 3 || Auth::user()->role_id == 4 || Auth::user()->role_id == 5 || Auth::user()->role_id == 6){ //admin pusat
            return view('vendor.voyager.index');

        }elseif(Auth::user()->role_id == 2){ //donatur
            dd("donatur");
        }elseif(Auth::user()->role_id == 3){ //petugas
            dd("petugas");
            
        }elseif(Auth::user()->role_id == 4){ //admin cabang
            dd("admin cabang");

        }elseif(Auth::user()->role_id == 5){ //administrator
            dd("administrator");

        }elseif(Auth::user()->role_id == 6){ //normal user
            // return Redirect::action('IssuerController@index');
            dd("normal user");

       
        }else{
    
        //   return  view('voyager::XXXXXXX.cardholder');
        }
    }
}
