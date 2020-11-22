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

class cabangImports implements WithHeadingRow, WithChunkReading, ToModel
{
        public function model(array $row)
        {
            // dd($row);
            return new CabangKotakamal([
                'id' => $row['id_cabang'],
                'nama_cabang' => $row['nama_cabang']
            ]);
        }

    public function chunkSize(): int
    {
        return 100;
    }
}