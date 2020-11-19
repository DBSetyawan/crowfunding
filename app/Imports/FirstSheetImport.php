<?php
namespace App\Imports;

use App\DonaturGroup;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class FirstSheetImport implements WithHeadingRow, ToModel, WithChunkReading
{
    // public function collection(Collection $rows)
    // {
        // foreach ($rows as $rows => $datae) 
        //         {
        //             // $data[$rows] = $datae->toArray();
        //             dd($datae);
        //             DonaturGroup::create($data);
              
        //         }

        // public function mapping(): array
        // {
        //     return [
        //         'kode_dn_groups'  => 'A2',
        //         'donatur_group_name' => 'B2',
        //     ];
        // }
        
        public function model(array $row)
        {

            //   foreach ($row as $rows => $datae) 
            //     {
            //         $data[$rows] = $datae;
            //         // DonaturGroup::create($data);
            //      return new DonaturGroup($datae);
            //     }
            return new DonaturGroup([
                'kode_dn_groups' => $row['kode_dn_groups'],
                'donatur_group_name' => $row['donatur_group_name'],
            ]);
                // dd($data);

         
        }
    // }

    public function chunkSize(): int
    {
        return 1000; //ANGKA TERSEBUT PERTANDA JUMLAH BARIS YANG AKAN DIEKSEKUSI
    }
}