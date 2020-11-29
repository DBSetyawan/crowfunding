<?php
namespace App\Imports;

use App\Amil;
use App\User;
use App\Donatur;
use App\Midtran;
use App\DonaturGroup;
use App\CabangKotakamal;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class UserAutomaticallyInsert implements WithHeadingRow, WithChunkReading, ToModel, WithCalculatedFormulas, ShouldAutoSize, ShouldQueue
{

    use Importable;

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
        
        DB::beginTransaction();
        try 
        {
            // dd($row);
            // ini_set('memory_limit','1024M');
            // set_time_limit(0);
            ini_set('max_execution_time', 0);
                // dd($row);
                $users = User::create([
                        'id' => (Int) $row['id_user'],
                        'users_id' => (Int) $row['id_user'],
                        'role_id' => (Int)  $row['id_role'], //admin cabang
                        'parent_id' => (Int)  $row['id_parrent'], //admin cabang
                        'add_by_user_id' => $row['add_by_id'],
                        'cabang_id' => (Int) $row['id_cabang'],
                        'amil_id' => (Int) $row['id_petugas'],
                        'groups_id' => (Int) $row['id_group_donatur'],
                        'name' => $row['nama_user'],
                        'alamat' => $row['alamat_donatur'],
                        'password' => \Hash::make('88888888'),
                        'email' =>  $row['id_user'].'@kotakamal.care',
                    ]
                );

                $donatur = Donatur::create([
                    'added_by_user_id' => (Int) $users['id'],
                    'id_cabang' => (Int) $users['cabang_id'],
                    'donaturs_id' => $row['id_user'],
                    'user_id' => (Int) $users['id'],
                    'donatur_group_id' => (Int) $users['groups_id'],
                    'nama' => $users['name'],
                    'alamat' => $row['alamat_donatur']
                ]);

                return new Midtran([
                    'donatur_id' => (Int) $donatur['id'],
                    'id_cabang' => (Int) $users['cabang_id'],
                    'payment_status' => 'settlement',
                    'program_id' => (Int) $row['program'],
                    'amount' => $row['nominal'],
                    'group_id' => (Int) $users['groups_id'],
                    'added_by_user_id' => (Int) $users['id'],
                ]);

                    // if($midtrans == false){
        
                    //     DB::rollback();
                        
                    // }

                DB::commit();

            } catch (\Exception $e) {
                // Rollback Transaction
                DB::rollback();
                dd($e);
            }
         
        }

        // public function startRow(): int 
        // {
        //      return 1;
        // }

    public function chunkSize(): int
    {
        return 6000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

}