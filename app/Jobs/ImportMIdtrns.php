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

class ImportMIdtrns implements ShouldQueue
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
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $reader = Reader::createFromPath(storage_path('app/public/temp/' . $this->filename), 'r');
        // $reader->setHeaderOffset(0);
        // $records = Statement::create()->process($reader);
        // $records->getHeader();
        // // dd($records);
        // foreach ($records as $record) {
        //     dd($record);
        //     //do something here
        //     $check[] = [
        //         'id_role' => $record['ID USER;']
        //     ];
        // }
        // dd($check);
        // (new UserAutomaticallyInsert)->queue('public/temp/' . $this->filename)->allOnQueue('default');
        Excel::import(new UserAutomaticallyInsert, 'public/temp/' . $this->filename); //MENJALANKAN PROSES IMPORT
        unlink(storage_path('app/public/temp/' . $this->filename)); //MENGHAPUS FILE EXCEL YANG TELAH DI-UPLOAD
    }
}
