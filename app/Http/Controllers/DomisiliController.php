<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Excel;
use App\Imports\KotakamalImport;

class DomisiliController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */
    
     public function get_json(Request $request){


        $page= $request->page;
        $resultCount = 10;
        $end = ($page - 1) * $resultCount;       
        $start = $end + $resultCount;

        $count_filtered = DB::table('tbl_kelurahan')
        ->select('tbl_kelurahan.*',
        'tbl_kecamatan.kabkot_id','tbl_kecamatan.kecamatan',
        'tbl_kabkot.provinsi_id','tbl_kabkot.kabupaten_kota','tbl_kabkot.ibukota','tbl_kabkot.k_bsni',
        'tbl_provinsi.provinsi','tbl_provinsi.p_bsni',
        )
        ->join('tbl_kecamatan', 'tbl_kecamatan.id', '=', 'tbl_kelurahan.kecamatan_id')
        ->join('tbl_kabkot', 'tbl_kabkot.id', '=', 'tbl_kecamatan.kabkot_id')
        ->join('tbl_provinsi','tbl_provinsi.id', '=', 'tbl_kabkot.provinsi_id')

        ->where('tbl_kelurahan.kelurahan','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_kelurahan.kd_pos','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_kecamatan.kecamatan','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_kabkot.kabupaten_kota','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_kabkot.ibukota','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_kabkot.k_bsni','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_provinsi.provinsi','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_provinsi.p_bsni','LIKE','%'.$request->term.'%')
        ->count(); 
        $data = DB::table('tbl_kelurahan')
        ->select('tbl_kelurahan.*',
        'tbl_kecamatan.kabkot_id','tbl_kecamatan.kecamatan',
        'tbl_kabkot.provinsi_id','tbl_kabkot.kabupaten_kota','tbl_kabkot.ibukota','tbl_kabkot.k_bsni',
        'tbl_provinsi.provinsi','tbl_provinsi.p_bsni',
        )
        ->join('tbl_kecamatan', 'tbl_kecamatan.id', '=', 'tbl_kelurahan.kecamatan_id')
        ->join('tbl_kabkot', 'tbl_kabkot.id', '=', 'tbl_kecamatan.kabkot_id')
        ->join('tbl_provinsi','tbl_provinsi.id', '=', 'tbl_kabkot.provinsi_id')

        ->where('tbl_kelurahan.kelurahan','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_kelurahan.kd_pos','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_kecamatan.kecamatan','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_kabkot.kabupaten_kota','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_kabkot.ibukota','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_kabkot.k_bsni','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_provinsi.provinsi','LIKE','%'.$request->term.'%')
        ->orWhere('tbl_provinsi.p_bsni','LIKE','%'.$request->term.'%')
        ->offset($start)
        ->limit(10)
        ->get();

        return response([
            'results'=>$data,
            'count_filtered'=>$count_filtered
        ]);

        
     }


     public function import(Request $request){

        $path1 = $request->file('excel')->store('temp'); 
        $path=storage_path('app').'/'.$path1;  


        Excel::import(new KotakamalImport, $path);        
    }
}