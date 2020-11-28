<?php

namespace App\Imports;

use App\Imports\cabangImports;
use App\Imports\PetugasSheets;
use App\Imports\donatursSheets;
use App\Imports\DonaturGroupSheets;
use App\Imports\donatursonlineSheets;
use App\Imports\hisotrytransactionSheet;
use App\Imports\UserAutomaticallyInsert;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;

class donaturgImports implements WithMultipleSheets  
{
    use WithConditionalSheets;
    
    // public function sheets(): array
    // {
    //     return [
    //         'DATA GROUP' => new FirstSheetImport(),
    //         'DATA PETUGAS' => new PetugasSheets(),
    //     ];
    // }

    public function conditionalSheets(): array
    {
        return [
            'DATA GROUP' => new DonaturGroupSheets(),
            'HISTORY BULAN OKT 2020' => new UserAutomaticallyInsert(),
            'DATA PETUGAS' => new PetugasSheets(),
            'DATA CABANG' => new cabangImports(),
            'DATA DONATUR OFFLINE' => new donatursSheets(),
            'DATA DONATUR' => new donatursonlineSheets(),
            // 'HISTORY BULAN OKT 2020' => new hisotrytransactionSheet(),
            'History Bulan Okt 2020' => new hisotrytransactionSheet(),
            'history batch 2' => new hisotrytransactionSheet(),
        ];
    }
}