<?php
namespace App\Imports;

use App\User;
use App\Donatur;
use App\Midtran;
use App\DonaturGroup;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
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

            //   foreach ($row as $rows => $datae) 
            //     {
            //         $data[$rows] = $datae;
            //         // DonaturGroup::create($data);
            //      return new DonaturGroup($datae);
            //     }
            // dd($row);die;
            return new Midtran([
                'donatur_id' => $row['id_donatur'],
                'id_cabang' => $row['id_cabang'],
                'payment_status' => 'settlement',
                'program_id' => $row['program'],
                'amount' => $row['nominal'],
                'added_by_user_id' => $row['id_petugas'],
            ]);

        }

    public function chunkSize(): int
    {
        return 3000;
    }
}