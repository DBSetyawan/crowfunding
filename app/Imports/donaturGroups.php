<?php

namespace App\Imports;

use App\DonaturGroup;
use App\Imports\FirstSheetImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;

class donaturGroups implements WithMultipleSheets 
{

    // use Importable;
    // use RemembersChunkOffset;
    // public function model(array $row)
    // {
    //     // foreach ($row as $key => $value) {
    //     //     # code...
    //     //     $data_row[] = $value;
    //     // }
    // }
    // public function mapping(): array
    // {
    //     return [
    //         'ID GROUP'  => 'A2',
    //         'NAMA GROUP' => 'B2',
    //     ];
    // }
    
    // public function collection(Collection $row)
    // {
    //     $data =[];
    //     foreach ($row as $rows => $datae) 
    //         {
    //             // $data[$rows] = $datae->toArray();
    //             dd($datae->toArray());
    //             DonaturGroup::create($data);
          
    //         }
                  // return new DonaturGroup([
                //     'kode_dn_groups' => $row[0],
                //     'donatur_group_name' => (String) $row[1],
                // ]);

            // dd($data);

                // dd($data);
               
            // }
        //         dd($data);
       
            // }
            // dd($data["donatur_group_name"]);
            // DonaturGroup::create($data);
            // $sdasd = 
    // }

    public function sheets(): array
    {
        return [
            new FirstSheetImport()
        ];
    }

    // public function headingRow(): int
    // {
    //     return 1;
    // }

    // public function headingRow(): int
    // {
    //     return 2;
    // }

    // public function batchSize(): int
    // {
    //     return 1000;
    // }

    // public function chunkSize(): int
    // {
    //     return 1000;
    // }
    // public function collection(Collection $rows)
    // {
    //     foreach ($rows as $row => $datae) 
    //     {
    //         $donaturGroup[] = [
    //             'id' => $datae[0],
    //             'donatur_group_name' => $datae[1]
    //         ];
    //     }


    //     // dd($donaturGroup);
    //     $importTodonaturgroups = collect($donaturGroup)->except([0])->toArray();
    //     DonaturGroup::create($importTodonaturgroups);
    //     foreach ($importTodonaturgroups as $index => $d) 
    //     {
    //             // $arr[] = [
    //             //    'id' => $d['id'],
    //             //     'donatur_group_name' => $d['donatur_group_name']
    //             // ];

    //             // DonaturGroup::create([
    //             //     'id' => $d['id'],
    //             //     'donatur_group_name' => $d['donatur_group_name'],
                    
    //             // ]);
    //     }
    //     // foreach ($rows as $row) 
    //     // {
    //     //     DonaturGroup::create([
    //     //         'name' => $row[0],
    //     //     ]);
    //     // }
    //     // return DonaturGroup::create($arr);


    // }
    // public function batchSize(): int
    // {
    //     return 1000;
    // }
    // public function uniqueBy()
    // {
    //     return 'email';
    // }
}
