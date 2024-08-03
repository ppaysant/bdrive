<?php

namespace App\Console\Commands;

use App\Services\MyLibraryService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class ImportMyLibrary extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ml:import {mlpath : Path to My Library sqlite database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import from My Library sqlite database';

    /**
     * Execute the console command.
     */
    public function handle(MyLibraryService $mlService)
    {
        $mlService->import($this->argument('mlpath'), $this);
    }
}
