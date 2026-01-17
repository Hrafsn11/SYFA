<?php

namespace App\Jobs;

use App\Services\ImportExcel;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportExcelPortofolio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;
    public $tries = 3;

    protected $filePath;
    protected $id_laporan;

    /**
     * Create a new job instance.
     */
    public function __construct($path = null, $id_laporan = null)
    {
        $this->filePath = $path;
        $this->id_laporan = $id_laporan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new ImportExcel($this->filePath))->import();
    }

    public function failed(\Exception $e)
    {
        Log::error('Import Excel gagal', [
            'file' => $this->filePath,
            'error' => $e->getMessage(),
        ]);
    }
}
