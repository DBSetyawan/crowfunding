<?php

namespace App\Imports;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use DB;
use App\Donatur;
use App\DonaturGroup;
use App\User;
use App\Midtran;
use App\Program;
use App\ProgramCategory;

class KotakamalImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) 
        {
            if($key > 0 && isset($row['8'])){
                $id = (int)$row['3'];
                $donatur = Donatur::where('id',$id)->first();
                if(!$donatur){
                    // if(isset($row['0'])){
                    //     dd($row);
                    // }
                    $id_group = (int)$row['8'];
                    $donatur_group = DonaturGroup::where('id',$id_group)->first();
                    if(!$donatur_group){
                        $donatur_group = new DonaturGroup;
                        $donatur_group->id = $id_group;
                        $donatur_group->donatur_group_name = $row['9'];
                        $donatur_group->save();
                    }

                    $donatur = new Donatur;
                    $donatur->id= (int)$row['3'];
                    $donatur->nama= $row['4'];
                    $donatur->no_hp= $row['7'];
                    $donatur->pekerjaan= '-';
                    $donatur->alamat= $row['5'];
                    $donatur->kelurahan_id= 1;
                    $donatur->donatur_group_id= $id_group;

                    $id_funding = (int) $row['11'];
                    $nama_funding = $row['12'];

                    $funding = User::where('id',$id_funding)->first();

                    if(!$funding){
                        $funding = new User;
                        $funding->id=$id_funding;
                        $funding->role_id=4;
                        $funding->name=$nama_funding;
                        $funding->email=$id_funding.'@kotakamal.care';
                        $funding->password='$2y$10$u/xtAz7zaarZHBZLx.msZOJUkX0bNBLwCfrLGMyD7HTl/r0Vo4QIS';
                        // $funding->settings='{"locale":"en"}';
                        $funding->urban_id=1;
                        $funding->save();

                        DB::table('user_roles')->insert([
                            'user_id'=>$funding->id,
                            'role_id'=>$funding->role_id,
                        ]);
                    }



                    $donatur->added_by_user_id = $funding->id;
                    $donatur->user_id = 0;
                    $donatur->save();


                    $program_name = $row['13'];

                    $program = Program::where('program_name',$program_name)->first();
                    if(!$program){

                        $program_category = ProgramCategory::where('program_cateogory_name','Infaq')->first();
                        if(!$program_category){
                            $program_category = ProgramCategory::create([
                                'program_cateogory_name'=>'Infaq'
                            ]);
                        }
                        $program = Program::create([
                            'program_name'=>$program_name,
                            'program_category_id'=>$program_category->id,
                            'type'=>'program',
                        ]);
                    }

                    //donation
                    $new_created = Midtran::create([
                        'amount'=>$row['14'],
                        'paid_date'=>date("Y-m-d H:i:s"),
                        'payment_gateway'=>'offline',
                        'payment_status'=>'settlement',
                        'donatur_id'=>$donatur->id,
                        'transaction_time'=>date("Y-m-d H:i:s"),
                        'program_id'=>$program->id,
                        'created_at'=>date("Y-m-d H:i:s"),
                        'updated_at'=>date("Y-m-d H:i:s"),
                        'added_by_user_id'=>$donatur->added_by_user_id,
                    ]);

                }

            }
            
        }
    }
}
