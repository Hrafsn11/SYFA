<?php 

namespace App\Services;

use App\Enums\NamaSBUEnum;
use App\Models\NilaiLaporan;
use App\Models\DetailLaporan;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ImportExcel
{
    protected $path;
    protected $section;
    protected $tahun;
    protected $id_laporan;

    public function __construct($filePath = null, $section = null, $tahun = null, $id_laporan = null)
    {
        $this->path = $filePath;
        $this->section = $section;
        $this->tahun = $tahun;
        $this->id_laporan = $id_laporan;
    }

    public function import()
    {
        if (!in_array($this->section, NamaSBUEnum::getConstants())) {
            throw new \Exception(
                'Import failed: Invalid section provided.',
            );
        }

        $filePath = public_path('storage/' . $this->path);

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $data = $this->mappingData($sheet);

        $getFileName = NamaSBUEnum::getMapping($this->section);
        $class = "\\App\\Services\\{$getFileName}";
        $mappingData = $class::from($data, $this->tahun, $this->id_laporan)->mapping();
        
        try {
            DB::beginTransaction();
            DetailLaporan::where('id_laporan_investasi', $this->id_laporan)->delete();
            DetailLaporan::insert($mappingData['detail_laporan']);
            NilaiLaporan::insert($mappingData['nilai_laporan']);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \Exception(
                'Import failed: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    private function mappingData($sheet)
    {
        $cellMap = [];

        // $highestRow = $sheet->getHighestRow();
        $highestRow = 300;
        // $highestColumn = $sheet->getHighestColumn();
        // $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
         // Batasi sampai kolom X
        $maxColumnIndex = Coordinate::columnIndexFromString('X'); // = 24

        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 1; $col <= $maxColumnIndex; $col++) {
                $cellAddress = Coordinate::stringFromColumnIndex($col) . $row;
                $cellMap[$cellAddress] = $sheet->getCell($cellAddress)->getValue();
            }
        }

        return $cellMap;
    }
}