<?php

if (!function_exists('rupiahFormatter')) {
    /**
     * Format angka menjadi format rupiah dengan pemisah ribuan
     *
     * @param mixed $value Nilai yang akan diformat (bisa string, int, atau float)
     * @param string $separator Pemisah ribuan (default: ',' untuk koma, bisa juga '.' untuk titik)
     * @return string Format rupiah seperti "Rp 1,000,000,000"
     */
    function rupiahFormatter($value, $separator = ',')
    {
        // Handle null, empty, atau non-numeric values
        if ($value === null || $value === '' || $value === false) {
            return 'Rp 0';
        }

        // Convert ke numeric, hapus format yang sudah ada jika ada
        if (is_string($value)) {
            // Hapus "Rp", spasi, dan karakter non-digit kecuali titik dan koma
            $cleaned = preg_replace('/Rp\.?\s*/i', '', $value);
            $cleaned = preg_replace('/[^\d,.-]/', '', $cleaned);
            
            // Hapus pemisah ribuan yang sudah ada (koma atau titik)
            $cleaned = str_replace([',', '.'], '', $cleaned);
            
            $value = (float) $cleaned;
        }

        // Convert ke integer (tanpa desimal)
        $value = (int) round($value);

        // Format dengan pemisah ribuan
        $formatted = number_format($value, 0, '.', $separator);

        return 'Rp ' . $formatted;
    }
}

if (!function_exists('rupiahToRawValue')) {
    /**
     * Mengkonversi format mata uang (Rp 200,000) menjadi angka normal (200000)
     *
     * @param mixed $value Nilai yang akan dikonversi (bisa string dengan format "Rp 200,000" atau angka)
     * @return float Angka normal tanpa format mata uang
     */
    function rupiahToRawValue($value)
    {
        if (empty($value)) {
            return 0;
        }

        // Jika sudah berupa angka, langsung return
        if (is_numeric($value)) {
            return (double) $value;
        }

        // Hapus "Rp", "Rp.", dan spasi
        $cleaned = preg_replace('/Rp\.?\s*/i', '', $value);
        
        // Hapus semua karakter non-digit kecuali titik dan koma
        $cleaned = preg_replace('/[^\d,.]/', '', $cleaned);
        
        // Hapus koma dan titik (sebagai pemisah ribuan)
        $cleaned = str_replace([',', '.'], '', $cleaned);
        
        // Konversi ke double
        return (double) $cleaned;
    }
}

if (!function_exists('parseCarbonDate')) {
    /**
     * Parse tanggal menggunakan Carbon dengan format yang ditentukan
     *
     * @param mixed $value Nilai tanggal yang akan di-parse (string, Carbon instance, atau null)
     * @param string $format Format tanggal (default: 'd/m/Y')
     * @return \Carbon\Carbon|null Instance Carbon hasil parse atau null jika gagal
     */
    function parseCarbonDate($value, string $format = 'd/m/Y')
    {
        // Jika null atau empty, return null
        if (empty($value)) {
            return null;
        }

        // Jika sudah berupa Carbon instance, langsung return
        if ($value instanceof \Carbon\Carbon) {
            return $value;
        }

        // Jika sudah berupa DateTime instance, convert ke Carbon
        if ($value instanceof \DateTime) {
            return \Carbon\Carbon::instance($value);
        }

        // Jika berupa string, parse dengan format yang ditentukan
        try {
            return \Carbon\Carbon::createFromFormat($format, $value);
        } catch (\Exception $e) {
            // Jika gagal dengan format yang ditentukan, coba parse dengan format default Carbon
            try {
                return \Carbon\Carbon::parse($value);
            } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('getFileName')) {
    /**
     * Mendapatkan nama file dari berbagai format (array, TemporaryUploadedFile, atau string)
     *
     * @param mixed $file File dalam format array dengan key 'name', TemporaryUploadedFile object, atau string path
     * @return string|null Nama file atau null jika tidak ada
     */
    function getFileName($file)
    {
        if (empty($file)) {
            return null;
        }

        // Jika berupa array dengan key 'name' (format dari storeFile)
        if (is_array($file) && isset($file['name'])) {
            return $file['name'];
        }

        // Jika berupa TemporaryUploadedFile atau UploadedFile
        if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile || 
            $file instanceof \Illuminate\Http\UploadedFile) {
            return $file->getClientOriginalName();
        }

        // Jika berupa string (path), ambil nama file dari path
        if (is_string($file)) {
            return basename($file);
        }

        return null;
    }
}

if (!function_exists('getRelativePath')) {
    /**
     * Mendapatkan path relatif dari TemporaryUploadedFile (contoh: livewire-tmp/xxxxx.pdf)
     * 
     * @param mixed $file TemporaryUploadedFile, UploadedFile, string, atau null
     * @return string|null Path relatif seperti "livewire-tmp/xxxxx.pdf" atau null
     */
    function getRelativePath($file)
    {
        if (empty($file)) {
            return null;
        }

        // Jika sudah berupa string (path), langsung return
        if (is_string($file)) {
            return $file;
        }

        // Jika berupa TemporaryUploadedFile, ambil path relatif menggunakan Reflection
        if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $reflection = new \ReflectionClass($file);
            $pathProperty = $reflection->getProperty('path');
            $pathProperty->setAccessible(true);
            return $pathProperty->getValue($file);
        }

        // Jika berupa UploadedFile biasa, tidak bisa mendapatkan path relatif
        // karena file sudah di-upload ke storage permanen
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            return null;
        }

        return null;
    }
}

if (!function_exists('getFileUrl')) {
    /**
     * Mendapatkan URL untuk preview atau download file dari livewire-tmp
     * 
     * @param mixed $file TemporaryUploadedFile, UploadedFile, string (path), atau null
     * @param bool $forceDownload Jika true, akan memaksa download. Jika false, akan preview di tab baru jika memungkinkan
     * @return string|null URL untuk mengakses file atau null
     */
    function getFileUrl($file, $forceDownload = false)
    {
        if (empty($file)) {
            return null;
        }

        // Jika berupa TemporaryUploadedFile
        if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            // Untuk preview (buka di tab baru tanpa force download)
            // SELALU gunakan custom preview route untuk memastikan tidak force download
            if (!$forceDownload) {
                return \Illuminate\Support\Facades\URL::temporarySignedRoute(
                    'file.preview',
                    now()->addMinutes(30),
                    ['filename' => $file->getFilename()]
                );
            }
            
            // Jika forceDownload = true, gunakan Livewire preview route (akan force download)
            return \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'livewire.preview-file',
                now()->addMinutes(30),
                ['filename' => $file->getFilename()]
            );
        }

        // Jika berupa string (path), coba buat URL dari Storage
        if (is_string($file)) {
            // Jika path sudah lengkap dengan livewire-tmp, gunakan Storage
            if (str_contains($file, 'livewire-tmp')) {
                $disk = \Livewire\Features\SupportFileUploads\FileUploadConfiguration::disk();
                $storage = \Illuminate\Support\Facades\Storage::disk($disk);
                
                if ($storage->exists($file)) {
                    // Untuk preview (tidak force download), coba gunakan temporaryUrl jika tersedia
                    if (!$forceDownload) {
                        try {
                            $previewMimes = config('livewire.temporary_file_upload.preview_mimes', []);
                            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            
                            // Jika file bisa di-preview, gunakan temporaryUrl
                            if (in_array($extension, $previewMimes)) {
                                // Coba gunakan temporaryUrl jika adapter mendukung
                                if (method_exists($storage->getAdapter(), 'getTemporaryUrl')) {
                                    return $storage->temporaryUrl($file, now()->addMinutes(30));
                                }
                            }
                        } catch (\Exception $e) {
                            // Fallback ke URL biasa
                        }
                    }
                    
                    // Gunakan URL biasa dari Storage (browser akan handle preview/download)
                    return $storage->url($file);
                }
            }
            
            // Jika path sudah berupa URL, return langsung
            if (filter_var($file, FILTER_VALIDATE_URL)) {
                return $file;
            }
        }

        // Jika berupa UploadedFile biasa (sudah di storage permanen)
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            // File sudah di storage permanen, tidak bisa menggunakan temporaryUrl
            // Return null atau handle sesuai kebutuhan
            return null;
        }

        return null;
    }
}
}