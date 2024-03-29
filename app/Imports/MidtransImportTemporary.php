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
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
set_time_limit(0);
ini_set('max_execution_time', 0);
HeadingRowFormatter::default('none');
class MidtransImportTemporary implements ToCollection, WithStartRow, WithMappedCells, WithHeadingRow, WithChunkReading, WithCalculatedFormulas, ShouldAutoSize, ShouldQueue
{
    use Importable;

      public function mapping(): array
    {
        return [
            'ID CABANG'  => 'A1',
            'NAMA CABANG' => 'B1',
            'ID PETUGAS' => 'C1',
            'NAMA PETUGAS' => 'D1',
            'ID GROUP' => 'E1',
            'NAMA GROUP' => 'F1',
            'ID' => 'G1',
            'NAMA NAMA DONATUR' => 'H1',
            'ALAMAT DONATUR' => 'I1',
        ];
    }

    public function collection(Collection $row)
    {
        foreach ($row as $key => $value) {
            # code...
            $dump[$key] = $value;
            dd($row);
        }
        die;

        DB::beginTransaction();
        try 
        {
           
            dd($row);
                // set_time_limit(0);
                // ini_set('max_execution_time', 0);
                $donatur = Donatur::create([
                    'added_by_user_id' => (Int) $row['id'],
                    'id_cabang' => (Int) $row['ID CABANG'],
                    'donaturs_id' => $row['ID USER'],
                    'user_id' => (Int) $users['id'],
                    'donatur_group_id' => (Int) $users['groups_id'],
                    'nama' => $users['name'],
                    'alamat' => $row['ALAMAT DONATUR']
                ]);

                // set_time_limit(0);
                // ini_set('max_execution_time', 0);
                // return new Midtran([
                //     'donatur_id' => (Int) $donatur['id'],
                //     'id_cabang' => (Int) $users['cabang_id'],
                //     'payment_status' => 'settlement',
                //     'program_id' => (Int) ! is_null($row['PROGRAM']) ? $row['PROGRAM'] : 0,
                //     'amount' => ! is_null($row['NOMINAL']) ? $row['NOMINAL'] : 0,
                //     'group_id' => (Int) $users['groups_id'],
                //     'added_by_user_id' => (Int) $users['id'],
                // ]);

                    // if($midtrans == false){
        
                    //     DB::rollback();
                        
                    // }

                DB::commit();

            } catch (\Exception $e) {
                // Rollback Transaction
                DB::rollback();
                dd($e);
            }
         
        }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

}