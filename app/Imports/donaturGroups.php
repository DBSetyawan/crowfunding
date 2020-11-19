<?php

namespace App\Imports;

use App\DonaturGroup;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;

class donaturGroups implements WithMappedCells, ToModel, WithChunkReading, WithBatchInserts
{

    use Importable;
    use RemembersChunkOffset;
    // public function model(array $row)
    // {
    //     // foreach ($row as $key => $value) {
    //     //     # code...
    //     //     $data_row[] = $value;
    //     // }
    // }
    public function mapping(): array
    {
        return [
            'ids'  => 'A2',
            'donatur_group_names' => 'B2',
        ];
    }
    
    public function model(array $row)
    {
        $chunkOffset = $this->getChunkOffset();
        // dd($row);
       
        // foreach ($row as $rows => $datae) 
        //     {

                // $data[$rows] = $datae;
                return new DonaturGroup([
                    'kode_dn_groups' => $row["ids"],
                    'donatur_group_name' => $row["donatur_group_names"],
                ]);
            // }
            // dd($data["donatur_group_name"]);
            // DonaturGroup::create($data);
            // $sdasd = 
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
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
