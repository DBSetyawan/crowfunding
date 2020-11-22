<?php
namespace App\Imports;

use App\Amil;
use App\User;
use App\DonaturGroup;
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

            $petugas = Amil::create([
                'id' => $row['id_petugas'],
                'nama_petugas' => $row['nama_petugas'],
            ]);
            
            return new User([
                'name' => $petugas['nama_petugas'],
                'email' => $petugas['nama_petugas'].Str::random(2).'@gmail.com',
                'role_id' => 3,
                'password' => "88888888",
            ]);
         
        }

    public function chunkSize(): int
    {
        return 1000;
    }
}