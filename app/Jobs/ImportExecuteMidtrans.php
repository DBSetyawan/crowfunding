<?php

namespace App\Jobs;

use filename;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Bus\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use App\Imports\UserAutomaticallyInsert;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportExecuteMidtrans implements ShouldQueue
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
        (new UserAutomaticallyInsert)->queue(storage_path('app/public/temp/' . $this->filename));
        // Excel::queueImport(new UserAutomaticallyInsert, storage_path('app/public/temp/' . $this->filename)); //MENJALANKAN PROSES IMPORT
        unlink(storage_path('app/public/temp/' . $this->filename)); //MENGHAPUS filename EXCEL YANG TELAH DI-UPLOAD
    }

}
                                                                                            