<?php

namespace App\Jobs;

use filename;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Bus\Queueable;
use App\Imports\UserImportJobs;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use App\Imports\UserAutomaticallyInsert;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Imports\ImportFromJobsDonaturGroups;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportdonaturGroups implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filename;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function handle()
    {   
        
        // Excel::import($this->import, $this->filename);
        (new ImportFromJobsDonaturGroups)->queue(storage_path('app/public/temp/' . $this->filename));
        // Excel::queueImport(new UserAutomaticallyInsert, storage_path('app/public/temp/' . $this->filename)); //MENJALANKAN PROSES IMPORT
        unlink(storage_path('app/public/temp/' . $this->filename)); //MENGHAPUS filename EXCEL YANG TELAH DI-UPLOAD
    }

}
                                                                                            