<?php 

namespace App\Services;

use Illuminate\Support\Str;

class MappingRekursiData
{
    use HelperEvaluateSumExcel;

    protected function rekursiData($dataExcel, $parentId = null, $id_laporan_investasi = null)
    {
        $data = [];
        $dataNilai = [];
        foreach ($dataExcel as $key => $value) {
            $dataId = (string) Str::ulid();

            if (isset($value['child'])) {
                $childData = $this->rekursiData($value['child'], $dataId, $id_laporan_investasi);
                $data = array_merge($data, $childData['detail_laporan']);
                $dataNilai = array_merge($dataNilai, $childData['nilai_laporan']);
            }

            $nilaiPerTahun = (isset($value['child'])) ? (double) collect($data)->sum('nilai') : 0;

            if (isset($value['data'])) {
                $bulan = 1;

                for ($col = ord('E'); $col <= ord('P'); $col++) {
                    $cell = chr($col) . str_replace('E', '', $value['data'][0]);
                    $nilaiPerBulan = $this->data[$cell] ?? 0;

                    if (!is_numeric($nilaiPerBulan)) {
                        $nilaiPerBulan = (double) $this->evaluateSumFormula($nilaiPerBulan, $this->data);
                    }
    
                    $dataNilai[] = [
                        'id_nilai_laporan' => (string) Str::ulid(),
                        'id_detail_laporan' => $dataId,
                        'id_laporan_investasi' => $id_laporan_investasi,
                        'bulan' => $bulan,
                        'nilai' => (double) $nilaiPerBulan,
                    ];

                    $nilaiPerTahun += $nilaiPerBulan;
    
                    $bulan++;
                }
            }

            $data[] = [
                'id_detail_laporan' => $dataId,
                'id_laporan_investasi' => $id_laporan_investasi,
                'parent_id' => $parentId,
                'tahun' => $this->tahun,
                'komponen' => $value['component'],
                'nilai' => (double) $nilaiPerTahun,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return [
            'detail_laporan' => $data, 
            'nilai_laporan' => $dataNilai
        ];
    }
}