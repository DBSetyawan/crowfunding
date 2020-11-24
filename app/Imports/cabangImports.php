<?php
namespace App\Imports;

use App\User;
use App\CabangKotakamal;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class cabangImports implements WithHeadingRow, WithChunkReading, ToCollection
{
        public function collection(Collection $row)
        {
            // dd($row['nama_cabang']);
            // return new CabangKotakamal([
            //     'id' => $row['id_cabang'],
            //     'nama_cabang' => $row['nama_cabang']
            // ]);
            // dd($row);
            // $cabang = CabangKotakamal::all();
            // dd($cabang);
            foreach ($row as $value) {
                # code...
                $rd = $value->toArray();
                 User::create([
                   'name' => $rd['nama_cabang'],
                   'email' =>  $rd['nama_cabang'].'@kotakamal.care',
                   'password' => bcrypt('88888888'),
                   'role_id' => 3
               ]);
            }
        }

    public function chunkSize(): int
    {
        return 100;
    }
}