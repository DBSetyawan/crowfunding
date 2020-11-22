<?php
namespace App\Imports;

use App\User;
use App\Donatur;
use App\DonaturGroup;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class donatursSheets implements WithHeadingRow, WithChunkReading, ToModel, WithCalculatedFormulas
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
            $donatur = Donatur::create([
                'added_by_user_id' => $row['id_petugas'],
                'id_cabang' => $row['id_cabang'],
                'user_id' => $row['id_petugas'],
                'id' => $row['id_donatur'],
                'donatur_group_id' => $row['id_group_donatur'],
                'nama' => $row['nama_nama_donatur'],
                'alamat' => $row['alamat_donatur']
            ]);

            return new User([
                'name' => $donatur['nama'],
                'email' => $donatur['nama'].Str::random(count(auth()->user()->id)+1).Str::random(4).'@gmail.com',
                'password' => "88888888",
                'role_id' => 4,
                'alamat' => $donatur['alamat'],
            ]);
         
        }

        // public function collection(Collection $rows)
        // {
        //     foreach ($rows as $row => $dump) 
        //     {
                
        //         if($dump['id_petugas'] < 112){
        //             $sdas[] = $dump['id_petugas'];
        //         } elseif($dump['id_petugas'] == 201) {
        //             return "201 ok".$dump['id_petugas'];
    
        //         } elseif($dump['id_petugas'] == 301){
        //             return "301 ok".$dump['id_petugas'];
    
        //         } elseif($dump['id_petugas'] ==401){
        //             return "401 ok".$dump['id_petugas'];
    
        //         }elseif($dump['id_petugas'] ==501){
        //             return "501 ok".$dump['id_petugas'];
    
        //         }elseif($dump['id_petugas'] ==601){
        //             return "601 ok".$dump['id_petugas'];
    
        //         }elseif($dump['id_petugas'] == 701){
        //             return "701 ok".$dump['id_petugas'];
    
        //         } elseif($dump['id_petugas'] == 801){
        //             return "801 ok".$dump['id_petugas'];
    
        //         } else{
        //             return "unknown";
        //         }
                
        //     }
        //     dd($sdas);
        //     die;
         
        // }

    public function chunkSize(): int
    {
        return 2000;
    }
}