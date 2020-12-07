<?php
namespace App\Imports;

use App\Amil;
use App\User;
use App\Donatur;
use App\Midtran;
use App\DonaturGroup;
use App\CabangKotakamal;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

HeadingRowFormatter::default('none');

class ImportFromJobsDonaturGroups implements WithStartRow, WithCustomCsvSettings, WithEvents, WithHeadingRow, WithChunkReading, ToModel, WithCalculatedFormulas, ShouldAutoSize, ShouldQueue
{
    use Importable, RegistersEventListeners;

    public function startRow(): int
    {
        return 2;
    }
    
    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 50;
    }

    public function model(array $row)
    {
        
        DB::beginTransaction();

        try 
            {
                set_time_limit(0);
                ini_set('max_execution_time', 0);
                    dd($row);die;
                    DonaturGroup::create([
                        'id' => $row['ID GROUP DONATUR'],
                        'donatur_group_name' => $row['NAMA GROUP'],
                        'id_petugas' => $row['ID PETUGAS'],
                        'id_cabang' => $row['ID CABANG'],
                        'id_parent' => $row['ID PARRENT'],
                        'id_users' => $row['ID USER'],
                        'add_by_user_id' => $row['ADD BY ID']
                    ]);

                    DB::commit();

            } catch (\Exception $e) {
                DB::rollback();
                dd($e);
            }
         
        }

        public function getCsvSettings(): array
        {
            return [
                'input_encoding' => 'ISO-8859-1',
                'delimiter' => ',',
            ];
        }

}