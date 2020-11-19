<?php

namespace App\Imports;

use App\Imports\PetugasSheets;
use App\Imports\FirstSheetImport;
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
            'DATA GROUP' => new FirstSheetImport(),
            'DATA PETUGAS' => new PetugasSheets(),
            // 'Worksheet 3' => new ThirdSheetImport(),
        ];
    }
}