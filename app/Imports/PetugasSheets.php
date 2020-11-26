<?php
namespace App\Imports;

use App\Amil;
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

class PetugasSheets implements WithHeadingRow, WithChunkReading, ToModel, WithCalculatedFormulas
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
        $donaturg = DonaturGroup::create([
            'id' => $row['id_group'],
            'donatur_group_name' => $row['nama_group'],
        ]);
        $cabangs = CabangKotakamal::create([
            'id' => $row['id_petugas'],
            'nama_cabang' => $row['nama_petugas']
        ]);
            $petugas = Amil::create([
                'id' => $row['id_petugas'],
                'nama_petugas' => $row['nama_petugas'],
            ]);

                User::create([
                    'password' => bcrypt('88888888'),
                    'groups_id' => $donaturg['id'],
                    'additional_each_id' => $cabangs['id'],
                    'role_id' => 3, //petugas
                    'name' => $petugas['nama_petugas'].'-AMIL',
                    'email' =>  $petugas['nama_petugas'].'-AMIL'.Str::random(4).'@kotakamal.care',
               ]);

               return new User([
                'password' => bcrypt('88888888'),
                'groups_id' => $donaturg['id'],
                'additional_each_id' => $cabangs['id'],
                'role_id' => 2, //admin cabang
                'name' => $petugas['nama_petugas'].'-ADMIN CABANG',
                'email' =>  $petugas['nama_petugas'].'-ADMIN CABANG'.Str::random(4).'@kotakamal.care',
           ]);
         
        }

    public function chunkSize(): int
    {
        return 1000;
    }
}