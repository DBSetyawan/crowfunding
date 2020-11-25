<?php
namespace App\Imports;

use App\User;
use App\DonaturGroup;
use App\CabangKotakamal;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class cabangImports implements WithHeadingRow, WithChunkReading, ToModel, WithCalculatedFormulas
{
        public function model(array $row)
        // public function collection(Collection $row)
        {
            // dd($row);

            $cabangs = CabangKotakamal::create([
                'id' => $row['id_cabang'],
                'nama_cabang' => $row['nama_cabang']
            ]);

                $nama_cabang = $cabangs['nama_cabang'];
                $id_cabang = $cabangs['id'];
                $group_id = $donaturg['id'];

                    return new User([
                        'password' => bcrypt('88888888'),
                        'groups_id' => $group_id,
                        'additional_each_id' => $id_cabang,
                        'role_id' => 2,
                        'name' => $nama_cabang,
                        'email' =>  $nama_cabang.'@kotakamal.care',
                   ]);
            // dd($row);
            // $cabang = CabangKotakamal::all();
            // dd($cabang);
            // foreach ($row as $value) {
            //     # code...
            //     $rd = $value->toArray();
            //      User::create([
            //        'name' => $rd['nama_cabang'],
            //        'email' =>  $rd['nama_cabang'].'@kotakamal.care',
            //        'password' => bcrypt('88888888'),
            //        'role_id' => 3
            //    ]);
            // }
        }

    public function chunkSize(): int
    {
        return 1000;
    }
}