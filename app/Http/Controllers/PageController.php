<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use DB;
use Config;
use Illuminate\Http\Request;
use App\Donatur;
use App\Midtran;
use Auth;
class PageController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */
    public function index()
    {
        $sliders = DB::table('sliders')->get();
        foreach ($sliders as $key => $slider) {
            $sliders[$key]->image=Config::get('app.admin_asset_base_url').'/'.$slider->image;
        }

        $program_categories = DB::table('program_categories')->get();
        foreach ($program_categories as $key => $item) {
            $program_categories[$key]->icon=Config::get('app.admin_asset_base_url').'/'.$item->icon;
        }

        $campaigns = DB::table('programs')->select('id','program_name','type','start_date','end_date','target_amount','program_category_id','thumbnail','created_at','updated_at')->where('type','campaign')->get();

        foreach ($campaigns as $key => $item) {
            $campaigns[$key]->total= DB::table('midtrans')->where('program_id',$item->id)->where('payment_status','settlement')->sum('amount');
            $campaigns[$key]->percentage = $campaigns[$key]->total/$item->target_amount*100;
            $campaigns[$key]->thumbnail =Config::get('app.admin_asset_base_url').'/'.$item->thumbnail;
            $campaigns[$key]->days_left = get_days_between_dates(date("Y/m/d"),$item->end_date);
        }


        $programs = DB::table('programs')->select('id','program_name','type','start_date','end_date','target_amount','program_category_id','thumbnail','created_at','updated_at')->where('type','program')->get();

        foreach ($programs as $key => $item) {
            $programs[$key]->total= DB::table('midtrans')->where('program_id',$item->id)->where('payment_status','settlement')->sum('amount');
            $programs[$key]->percentage = 100;
            $programs[$key]->thumbnail =Config::get('app.admin_asset_base_url').'/'.$item->thumbnail;
        }

        // dd($campaigns);

        // dd($program_categories);
        return view('menus.home',compact('sliders','program_categories','campaigns','programs'));
    }

    public function program_detail(Request $request,$tipe,$id){

        $program = DB::table('programs')->where('id',$id)->first();
        if(!$program){
            return abort(404);
        }

        $program->program_category = DB::table('program_categories')->where('id',$program->program_category_id)->first();
        $program->thumbnail = Config::get('app.admin_asset_base_url').'/'.$program->thumbnail;
        $program->total= DB::table('midtrans')->where('program_id',$program->id)->where('payment_status','settlement')->sum('amount');
        $program->count_donations= DB::table('midtrans')->where('program_id',$program->id)->where('payment_status','settlement')->count();
        
        $program->latest_donations = DB::table('midtrans')->select('midtrans.*','donaturs.nama as donatur_name')->join('donaturs', 'donaturs.id', '=', 'midtrans.donatur_id')
        ->where('midtrans.program_id',$program->id)->where('payment_status','settlement')->orderBy('created_at', 'desc')->take(10)->get();
        if($program->type == "campaign"){
            $program->percentage = $program->total/$program->target_amount*100;
            $program->days_left = get_days_between_dates(date("Y/m/d"),$program->end_date);
        }
        
        
        // return response([$program]);
        return view('program.detail',compact('program'));
    }

    public function search(Request $request){
        $tipe = null;
        if(isset($request->tipe)){
            if($request->tipe == "campaign" || $request->tipe == "program"){
                $tipe = $request->tipe;
            }
        }
        $categories = DB::table('program_categories')->get();
        foreach ($categories as $key => $item) {
            $categories[$key]->icon=Config::get('app.admin_asset_base_url').'/'.$item->icon;
        }

        $programs = DB::table('programs')->select('id','program_name','type','start_date','end_date','target_amount','program_category_id','thumbnail','type','created_at','updated_at');
        $keyword = "";
        $sort = null;
        $category = null;
        if(isset($request->category)){
            $category = DB::table('program_categories')->where('id',$request->category)->first();
            if(!$category){
                return abort(404);
            }
            $programs->where('program_category_id',$request->category);
        }

        if(isset($request->keyword)){
            $keyword = $request->keyword;
            $programs->where('program_name','like','%'.$request->keyword.'%');
        }

        if(isset($request->sort)){
            if($request->sort == "date-asc"){
                $sort = (object) array('text'=>'Tanggal Terlama','value'=>'date-asc');
                $programs->orderBy('start_date', 'asc');
            }else if($request->sort == "date-desc"){
                $sort = (object) array('text'=>'Tanggal Terbaru','value'=>'date-desc');
                $programs->orderBy('start_date', 'desc');
            }
        }
        if($tipe){
            $programs->where('type',$tipe);
        }
        
        $programs=$programs->get();
        foreach ($programs as $key => $item) {
            $programs[$key]->total= DB::table('midtrans')->where('program_id',$item->id)->where('payment_status','settlement')->sum('amount');
            $programs[$key]->thumbnail =Config::get('app.admin_asset_base_url').'/'.$item->thumbnail;
            if($programs[$key]->type == "campaign"){
                $programs[$key]->percentage = $programs[$key]->total/$item->target_amount*100;
                $programs[$key]->days_left = get_days_between_dates(date("Y/m/d"),$item->end_date);
            }
        }

       

        return view('menus.search',compact('programs','category','sort','keyword','categories','tipe'));
    }

    public function donations(Request $request){
        $donatur = Donatur::where('user_id',Auth::user()->id)->first();


        $data = Midtran::where('donatur_id',$donatur->id)->orderBy('created_at','desc')->get();

        foreach ($data as $key => $d) {
            $program = DB::table('programs')->select('id','program_name','type','start_date','end_date','target_amount','program_category_id','thumbnail','created_at','updated_at')->first();
            $program->thumbnail =Config::get('app.admin_asset_base_url').'/'.$program->thumbnail;
            $d->program = $program;
        }

        // $data = [];

        return view('menus.donations',compact('data'));
    }
}