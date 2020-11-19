<?php

namespace App\Console\Commands;

use App\Imports\donaturGroups;
use Illuminate\Console\Command;

class ImportExcel extends Command
{
    protected $signature = 'import:excel';

    protected $description = 'Laravel Excel importer';

    public function handle()
    {
        $this->output->title('Starting import');
        (new donaturGroups)->withOutput($this->output)->import('storega0VtgnjVUpDYZRSQlyncGqyQQ8C9Snj5RForpOE7L.xlsx');
        $this->output->success('Import successful');
    }
}