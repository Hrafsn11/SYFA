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

    protected $filePath;
    protected $section;
    protected $tahun;
    protected $id_laporan;

    /**
     * Create a new job instance.
     */
    public function __construct($path = null, $section = null, $tahun = null, $id_laporan = null)
    {
        $this->filePath = $path;
        $this->section = $section;
        $this->tahun = $tahun;
        $this->id_laporan = $id_laporan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('ImportExcelPortofolio START', [
            'file' => $this->filePath,
        ]);

        try {
            (new ImportExcel($this->filePath, $this->section, $this->tahun, $this->id_laporan))->import();
            Log::info('ImportExcelPortofolio DONE');
        } catch (\Throwable $e) {
            Log::error('ImportExcelPortofolio ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            throw $e; // biar queue tahu ini gagal
        }
    }

    public function failed(\Exception $e)
    {
        Log::error('Import Excel gagal', [
            'file' => $this->filePath,
            'error' => $e->getMessage(),
        ]);
    }
}
