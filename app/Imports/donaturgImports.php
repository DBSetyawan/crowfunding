<?php

namespace App\Imports;

use App\Imports\FirstSheetImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;

class donaturgImports implements WithMultipleSheets  
{
    use WithConditionalSheets;
    
    public function sheets(): array
    {
        return [
            'DATA GROUP' => new FirstSheetImport(),
        ];
    }

    public function conditionalSheets(): array
    {
        return [
            'DATA GROUP' => new FirstSheetImport(),
            // 'Worksheet 2' => new SecondSheetImport(),
            // 'Worksheet 3' => new ThirdSheetImport(),
        ];
    }
}