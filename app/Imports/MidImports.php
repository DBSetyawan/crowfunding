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
// HeadingRowFormatter::default('none');
class MidImports implements ToModel, WithStartRow, WithBatchInserts, WithHeadingRow, WithChunkReading, WithCalculatedFormulas, ShouldAutoSize, ShouldQueue
{
    use Importable;

    //   public function mapping(): array
    // {
    //     return [
    //         'ID CABANG'  => 'A1',
    //         'NAMA CABANG' => 'B1',
    //         'ID PETUGAS' => 'C1',
    //         'NAMA PETUGAS' => 'D1',
    //         'ID GROUP' => 'E1',
    //         'NAMA GROUP' => 'F1',
    //         'ID' => 'G1',
    //         'NAMA NAMA DONATUR' => 'H1',
    //         'ALAMAT DONATUR' => 'I1',
    //     ];
    // }

    public function model(array $row)
    {
        try 
        {

            // dd($row);
            DB::beginTransaction();
           
                Donatur::create([
                    'added_by_user_id' => (Int) $row['id_user'],
                    'id_cabang' => (Int) $row['id_cabang'],
                    'donaturs_id' => $row['id_user'],
                    'user_id' => (Int) $row['id_user'],
                    'donatur_group_id' => (Int) $row['id_group_donatur'],
                    'nama' => $row['nama_user'],
                    'alamat' => $row['alamat_donatur']
                ]);

                // set_time_limit(0);
                // ini_set('max_execution_time', 0);
                Midtran::create([
                    'donatur_id' => (Int) $row['id_user'],
                    'id_cabang' => (Int) $row['id_cabang'],
                    'payment_status' => 'settlement',
                    'program_id' => (Int) ! is_null($row['program']) ? $row['program'] : 0,
                    'amount' => ! is_null($row['nominal']) ? $row['nominal'] : 0,
                    'group_id' => (Int) $row['id_group_donatur'],
                    'added_by_user_id' => (Int) $row['id_user'],
                ]);

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
        return 3000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

}
