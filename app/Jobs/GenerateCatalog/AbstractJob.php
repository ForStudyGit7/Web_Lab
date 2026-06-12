<?php

namespace App\Jobs\GenerateCatalog;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AbstractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue('generate-catalog'); // Черга за замовчуванням для всього ланцюга
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->debug('done');
    }

    /**
     * Кастомний метод для гарного виведення логів у файл laravel.log
     */
    protected function debug(string $msg)
    {
        $class = static::class;
        $msg = $msg . " [{$class}]";
        \Log::info($msg);
    }
}
