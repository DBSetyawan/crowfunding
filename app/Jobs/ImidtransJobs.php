<?php

namespace App\Jobs;

use File;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Bus\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use App\Imports\UserAutomaticallyInsert;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImidtransJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file)
    {
        $this->file = $file;
    }
    public function handle()
    {
        // Excel::import($this->import, $this->file);
        Excel::import(new UserAutomaticallyInsert, File::get(storage_path('app/temp/' . $this->file))); //MENJALANKAN PROSES IMPORT
        unlink(storage_path('app/temp/' . $this->file)); //MENGHAPUS FILE EXCEL YANG TELAH DI-UPLOAD
    }
}
