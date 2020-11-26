<?php
namespace App\Imports;

use App\User;
use App\Donatur;
use App\DonaturGroup;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
// set_time_limit(300000000);
// ini_set('upload_max_filesize', '120M');
// ini_set('post_max_size', '120M');
// ini_set('max_input_time', 300);
// ini_set('max_execution_time', 300);
class donatursonlineSheets implements WithHeadingRow, WithChunkReading, ToModel, WithCalculatedFormulas, WithBatchInserts
{
    
        public function model(array $row)
        {

            $donaturs = Donatur::create([
                'added_by_user_id' => $row['id_petugas'],
                'id_cabang' => $row['id_cabang'],
                'id' => $row['id_donatur'],
                'user_id' => $row['id_petugas'],
                'donatur_group_id' => $row['id_group'],
                'nama' => $row['nama_donatur'],
                'alamat' => $row['alamat_donatur']  
            ]);

            return new User([
                'name' => $donaturs['nama'],
                'email' =>'DONATUR-'.Str::random(5).'@kotakamal.care',
                'password' => bcrypt('88888888'),
                'role_id' => 4,
                'alamat' => $donaturs['alamat'],
            ]);
         
        }

    public function chunkSize(): int
    {
        return 2000;
    }

    
    public function batchSize(): int
    {
        return 2000;
    }

}





















