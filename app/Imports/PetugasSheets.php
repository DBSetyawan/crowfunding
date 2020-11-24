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
            // $donaturg = DonaturGroup::create([
            //     'id' => $row['id_group'],
            //     'donatur_group_name' => $row['nama_group'],
            // ]);

            $id_groups = [10101
,10102
,10103
,10104
,10105
,10106
,10107
,10201
,10202
,10203
,10301
,10302
,10401
,10402
,10403
,10501
,10601
,10701
,10702
,10703
,10704
,10705
,10706
,10707
,10708
,10801
,10901
,10902
,10903
,11001
,11002
,11003
,11004
,11005
,11006
,11101
,11201
,11202
,11203
,11204
,11205
,11206
,11207
,11208
,11209
,11210
,11211
,11212
,11213
,11213
,11214
,11215
,20101
,20102
,20103
,20104
,20105
,20106
,30101
,30102
,30103
,30104
,30105
,30105
,30106
,30107
,30108
,30109
,30111
,30112
,30113
,30114
,30115
,40101
,40102
,40103
,40104
,40105
,50101
,50102
,50103
,50104
,50105
,50106
,60101
,70101
,80101];

                foreach ($id_groups as $key => $value) {
                    # code...
                     $data[] = [
                        'name' => $row['nama_petugas'],
                        'email' => $row['nama_petugas'].'@gmail.com',
                        'groups_id' => $value,
                        'role_id' => 3,
                        'password' => bcrypt('88888888')
                    ];
                }

                dd($data);
            
           
         
        }

    public function chunkSize(): int
    {
        return 1000;
    }
}