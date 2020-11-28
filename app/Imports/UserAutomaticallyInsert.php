<?php
namespace App\Imports;

use App\Amil;
use App\User;
use App\DonaturGroup;
use App\CabangKotakamal;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class UserAutomaticallyInsert implements WithHeadingRow, WithChunkReading, ToModel, WithCalculatedFormulas, ShouldAutoSize
{
        public function model(array $row)
        {

        //     $petugas = Amil::create([
        //         'id' => $row['id_petugas'],
        //         'nama_petugas' => $row['nama_petugas'],
        //     ]);
        //     // $donaturg = DonaturGroup::create([
        //     //     'id' => $row['id_group'],
        //     //     'donatur_group_name' => $row['nama_group'],
        //     // ]);
        //     return new User([
        //         'password' => bcrypt('88888888'),
        //         'role_id' => 3,
        //         'name' => $petugas['nama_petugas'].'-AMIL'.Str::random(1),
        //         'email' =>  $petugas['nama_petugas'].'-'.'AMIL@kotakamal.care',
        //    ]);
        // dd($row);
        // $donaturg = DonaturGroup::create([
        //     'id' => $row['id_group'],
        //     'donatur_group_name' => $row['nama_group'],
        // ]);
        // $cabangs = CabangKotakamal::create([
        //     'id' => $row['id_petugas'],
        //     'nama_cabang' => $row['nama_petugas']
        // ]);
        //     $petugas = Amil::create([
        //         'id' => $row['id_petugas'],
        //         'nama_petugas' => $row['nama_petugas'],
        //     ]);

        //         User::create([
        //             'password' => bcrypt('88888888'),
        //             'groups_id' => $donaturg['id'],
        //             'additional_each_id' => $cabangs['id'],
        //             'role_id' => 3, //petugas
        //             'name' => $petugas['nama_petugas'].'-AMIL',
        //             'email' =>  $petugas['nama_petugas'].'-AMIL'.Str::random(4).'@kotakamal.care',
        //        ]);
        try 
            {

                set_time_limit(0);
                DB::beginTransaction();

                $users = DB::table('users')->insert([
                        'id' => $row['id_user'],
                        'role_id' => $row['id_role'], //admin cabang
                        'parent_id' => $row['id_parrent'], //admin cabang
                        'add_by_user_id' => $row['add_by_id'],
                        'cabang_id' => $row['id_cabang'],
                        'amil_id' => $row['id_petugas'],
                        'groups_id' => $row['id_group_donatur'],
                        'name' => ! is_null($row['nama_user']) ? $row['nama_user'] : $row['id_parrent'],
                        'alamat' => $row['alamat_donatur'],
                        'password' => bcrypt('88888888'),
                        'email' =>  $row['id_user'].'@kotakamal.care',
                    ]
                );

                $midtrans = DB::table('midtrans')->insert([
                    'donatur_id' => $users['id'],
                    // 'id' => $row['id_history'],
                    'id_cabang' => $users['cabang_id'],
                    'payment_status' => 'settlement',
                    'program_id' => $row['program'],
                    'amount' => $row['nominal'],
                    'added_by_user_id' => $users['id'],
                ]);

                DB::commit();

            } catch (\Exception $e) {
                // Rollback Transaction
                DB::rollback();
                dd($e);
            }
         
        }

    public function chunkSize(): int
    {
        return 1000;
    }
}