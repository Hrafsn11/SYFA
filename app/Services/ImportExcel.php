<?php 

namespace App\Services;

use App\Models\SummaryLaporan;
use App\Models\LaporanInvestasi;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ImportExcel
{
    protected $path;

    public function __construct($filePath = null)
    {
        $this->path = $filePath;
    }

    public function import()
    {

        $filePath = public_path('storage/' . $this->path);

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $data = $this->mappingData($sheet);

        
        try {
            DB::beginTransaction();

            foreach ($data as $key => $value) {
                
            }

            for ($i=0; $i < 3; $i++) { 
                LaporanInvestasi::create([
                    'nama_sbu' => 'SBU Example '.$i,
                    'tahun' => date('Y'),
                    'edit_by' => 'import_excel',
                    'path_file' => $this->path
                ]);
            }

            // foreach ($rows as $index => $row) {
            //     if ($index === 0) continue; // skip header

            //     DB::table('users')->insert([
            //         'name'  => $row[0],
            //         'email' => $row[1],
            //         'created_at' => now(),
            //     ]);
            // }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \Exception(
                'Import failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );

        }
    }

    private function mappingData($sheet)
    {
        $cellMap = [];

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellAddress = Coordinate::stringFromColumnIndex($col) . $row;
                $cellMap[$cellAddress] = $sheet->getCell($cellAddress)->getValue();
            }
        }

        return $cellMap;
    }
}