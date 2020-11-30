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
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

HeadingRowFormatter::default('none');
class UserAutomaticallyInsert implements WithStartRow, WithHeadingRow, WithChunkReading, ToModel, WithCalculatedFormulas, ShouldAutoSize, ShouldQueue
{
    use Importable;

    // public function mapping(): array
    // {
    //     return [
    //         'id_user'  => 'A1',
    //         'id_role' => 'B1',
    //         'id_parrent' => 'C1',
    //         'add_by_id' => 'D1',
    //         'id_cabang' => 'E1',
    //         'nama_cabang' => 'F1',
    //         'id_petugas' => 'G1',
    //         'nama_petugas' => 'H1',
    //         'id_group_donatur' => 'I1',
    //         'nama_group' => 'J1',
    //         'nama_user' => 'K1',
    //         'alamat_donatur' => 'L1',
    //         'program' => 'M1',
    //         'nominal' => 'N1',
    //     ];
    // }

    // public function headings(): array
    // {
        // return [
        //     'ID USER',
        //     'ID ROLE',
        //     'ID PARRENT',
        //     'add_by_id',
        //     'ID CABANG',
        //     'NAMA CABANG',
        //     'ID PETUGAS',
        //     'NAMA PETUGAS',
        //     'ID GROUP',
        //     'NAMA GROUP',
        //     'NAMA USER',
        //     'ALAMAT DONATUR',
        //     'PROGRAM',
        //     'NOMINAL',
        // ];
        // return $this->columns;
    // }
    // public function headingRow(): int
    // {
    //     return 12;
    // }
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
            set_time_limit(0);
            ini_set('max_execution_time', 0);
                // $pow = array();
                // foreach ($row as $key => $value) {
                //     # code...
                //     $pow[$key] = $value;
                // }
                // dd($pow);die;
                dd($row);
                $users = User::create([
                        'id' => (Int) $row['id_user'],
                        'users_id' => (Int) $row['id_user'],
                        'role_id' => (Int)  $row['id_role'], //admin cabang
                        'parent_id' => (Int)  $row['id_parrent'], //admin cabang
                        'add_by_user_id' => $row['add_by_id'],
                        'cabang_id' => (Int) $row['id_cabang'],
                        'amil_id' => (Int) $row['id_petugas'],
                        'groups_id' => (Int) $row['id_group_donatur'],
                        'name' => ! is_null($row['nama_user']) ? $row['nama_user'] : $row['nama_petguas'],
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

        public function startRow(): int
{
    return 2;
}

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 500;
    }

}