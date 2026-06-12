<?php

namespace App\Jobs\GenerateCatalog;

class GeneratePricesFileChunkJob extends AbstractJob
{
    private $chunk;
    private $fileNum;

    /**
     * Create a new job instance.
     */
    public function __construct($chunk = null, $fileNum = null)
    {
        parent::__construct();
        $this->chunk = $chunk;
        $this->fileNum = $fileNum;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        parent::handle();
    }
}
