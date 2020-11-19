<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Routing\Controller;
use Validator;
use Illuminate\Http\Request;
use Hash;
use DB;
use App\User;
use App\Donatur;
class AuthController extends Controller {

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate()
    {
        if (Auth::attempt(['email' => $email, 'password' => $password, 'active' => 1],$remember))
        {
            return redirect()->intended('/');
        }else{
            return redirect('login');
        }
    }

    public function login(){
        return view('auth.login');
    }


    public function login_post(Request $request){

        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'password'=>'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('auth.login')
                        ->withErrors($validator)
                        ->withInput();
        }


        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], true)){
            return redirect()->intended('/')->with('message','Selamat datang kembali!');;
        }else {
            return redirect()->route('auth.login')->with('error','Email atau Password Salah.');
        }
    }


    public function register(){
        return view('auth.register');
    }

    public function register_post(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'password'=>'required|min:8|confirmed',
            'nama'=>'required',
            'no_hp'=>'required',
            'pekerjaan'=>'required',
            'alamat'=>'required',
            'urban_id'=>'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('auth.register')
                        ->withErrors($validator)
                        ->withInput();
        }

        $user = new User();
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->name = $request->nama;
        $user->role_id=5;
        $user->avatar="users/default.png";
        $user->settings='{"locale":"en"}';
        $user->alamat=$request->alamat;
        $user->urban_id=$request->urban_id;
        $user->pekerjaan=$request->pekerjaan;
        $user->no_whatsapp=$request->no_hp;
        $user->save();

        $donatur = new Donatur();
        $donatur->nama=$request->nama;
        $donatur->no_hp=$request->no_hp;
        $donatur->pekerjaan=$request->pekerjaan;
        $donatur->alamat=$request->alamat;
        $donatur->kelurahan_id=$request->urban_id;
        $donatur->donatur_group_id=0;
        $donatur->added_by_user_id=$user->id;
        $donatur->user_id=$user->id;
        $donatur->save();

        return redirect()->route('auth.login')->with('message', 'Registrasi berhasil.');
    }


    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }
    

    public function profile(Request $request){
        return view('menus.profile');
    }

    public function profile_edit(Request $request){
        $user = Auth::user();
        // dd($user);

        $kelurahan = \App\Kelurahan::where('id',$user->urban_id)->first();
        $selected_domisili = (object)array('value'=>'','text'=>'');
        if($kelurahan){
            $selected_domisili->value = $kelurahan->id;
            $selected_domisili->text = $kelurahan->kelurahan.', '.$kelurahan->kecamatan->kecamatan.', '.$kelurahan->kecamatan->kabkot->kabupaten_kota.', '.$kelurahan->kecamatan->kabkot->provinsi->provinsi.', '.$kelurahan->kd_pos;
        }
        return view('auth.edit-profile',compact('user','selected_domisili'));
    }

    public function profile_edit_post(Request $request){


        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'password'=>'confirmed',
            'nama'=>'required',
            'no_hp'=>'required',
            'pekerjaan'=>'required',
            'alamat'=>'required',
            'urban_id'=>'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('profile.edit')
                        ->withErrors($validator)
                        ->withInput();
        }

        $user = User::findorfail(Auth::user()->id);
        $donatur = Donatur::where('user_id',$user->id)->firstOrFail();

        $user->email = $request->email;
        $user->name = $request->nama;
        $user->alamat=$request->alamat;
        $user->urban_id=$request->urban_id;
        $user->pekerjaan=$request->pekerjaan;
        $user->no_whatsapp=$request->no_hp;
        

        $donatur->nama=$request->nama;
        $donatur->no_hp=$request->no_hp;
        $donatur->pekerjaan=$request->pekerjaan;
        $donatur->alamat=$request->alamat;
        $donatur->kelurahan_id=$request->urban_id;
        

        if(isset($request->password)){
            $user->password = Hash::make($request->password);
        }

        $donatur->save();
        $user->save();

        
        return "update success";
    }


    public function get_domisili_json(Request $request){


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

}