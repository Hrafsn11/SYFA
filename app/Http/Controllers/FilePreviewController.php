<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;

class FilePreviewController extends Controller
{
    /**
     * Preview file dari livewire-tmp tanpa force download
     * 
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function preview($filename)
    {
        // Validasi signature untuk keamanan
        abort_unless(request()->hasValidSignature(), 401);

        $disk = FileUploadConfiguration::disk();
        $storage = Storage::disk($disk);
        $filePath = FileUploadConfiguration::path($filename);

        // Pastikan file ada
        if (!$storage->exists($filePath)) {
            abort(404, 'File not found');
        }

        // Dapatkan mime type
        $mimeType = $storage->mimeType($filePath) ?: 'application/octet-stream';
        
        // Dapatkan path fisik file
        $physicalPath = $storage->path($filePath);
        
        // Pastikan file ada secara fisik
        if (!file_exists($physicalPath)) {
            abort(404, 'File not found');
        }

        // Gunakan response()->file() untuk preview (bukan download)
        // Header 'Content-Disposition: inline' akan membuat browser preview file, bukan download
        return response()->file($physicalPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filename) . '"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}

