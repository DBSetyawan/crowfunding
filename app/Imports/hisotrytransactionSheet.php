<?php
namespace App\Imports;

use App\User;
use App\Donatur;
use App\Midtran;
use App\DonaturGroup;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class hisotrytransactionSheet implements WithHeadingRow, WithChunkReading, ToModel, WithCalculatedFormulas
{
        public function model(array $row)
        {
            try{
                set_time_limit(0);
                DB::beginTransaction();
            // dd($row);

            $id_donatur = DB::table('midtrans')->insert([
                'donatur_id' => $row['id_donatur'],
                // 'id' => $row['id_history'],
                'id_cabang' => $row['id_cabang'],
                'payment_status' => 'settlement',
                'program_id' => $row['program'],
                'amount' => $row['nominal'],
                'added_by_user_id' => $row['id_petugas'],
            ]);

            $donaturs = DB::table('donaturs')->insert([
                'added_by_user_id' => $row['id_petugas'],
                'id_cabang' => $row['id_cabang'],
                'id' => $row['id_donatur'],
                'user_id' => $row['id_petugas'],
                'donatur_group_id' => $row['id_group_donatur'], //batch 1
                // 'donatur_group_id' => $row['id_group'],//batch 2
                'nama' => $row['nama_nama_donatur'], //batch 1
                // 'nama' => $row['nama_donatur'], //batch 2
                'alamat' => $row['alamat_donatur']  
            ]);

            DB::table('users')->insert([
                'name' => $row['nama_nama_donatur'],
                'email' =>$donaturs['id'].'@kotakamal.care',
                'password' => bcrypt('88888888'),
                'role_id' => 4,
                'alamat' => $row['nama_nama_donatur']
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