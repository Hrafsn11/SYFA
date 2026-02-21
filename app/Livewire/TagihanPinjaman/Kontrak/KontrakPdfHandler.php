<?php

namespace App\Livewire\PengajuanPinjaman\Kontrak;

use App\Models\PengajuanPeminjaman;
use App\Models\HistoryStatusPengajuanPinjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

/**
 * Trait KontrakPdfHandler
 * 
 * Menangani pembuatan dan preview kontrak PDF pada halaman detail peminjaman.
 * Menggunakan DomPDF untuk generate PDF.
 */
trait KontrakPdfHandler
{
    /**
     * Siapkan data kontrak dari pengajuan peminjaman.
     */
    protected function prepareKontrakData(): array
    {
        $pengajuan = PengajuanPeminjaman::with('debitur')
            ->where('id_pengajuan_peminjaman', $this->pengajuan->id_pengajuan_peminjaman)
            ->first();

        $latestHistory = HistoryStatusPengajuanPinjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
            ->whereNotNull('nominal_yang_disetujui')
            ->orderBy('created_at', 'desc')
            ->first();

        $nilaiPembiayaan = $latestHistory->nominal_yang_disetujui ?? $pengajuan->total_pinjaman ?? 0;
        $biayaAdmin = $pengajuan->biaya_administrasi ?? 0;

        return [
            'id_peminjaman' => $pengajuan->id_pengajuan_peminjaman,
            'no_kontrak' => $pengajuan->no_kontrak ?? '',
            'no_kontrak2' => $pengajuan->no_kontrak ?? '',
            'tanggal_kontrak' => now()->format('d F Y'),
            'nama_perusahaan' => 'SYNNOVAC CAPITAL',
            'nama_debitur' => $pengajuan->debitur->nama ?? 'N/A',
            'nama_pimpinan' => $pengajuan->debitur->nama_ceo ?? 'N/A',
            'alamat' => $pengajuan->debitur->alamat ?? 'N/A',
            'tujuan_pembiayaan' => $pengajuan->tujuan_pembiayaan ?? 'N/A',
            'jenis_pembiayaan' => $pengajuan->jenis_pembiayaan ?? 'Invoice Financing',
            'nilai_pembiayaan' => 'Rp. ' . number_format($nilaiPembiayaan, 0, ',', '.'),
            'hutang_pokok' => 'Rp. ' . number_format($nilaiPembiayaan, 0, ',', '.'),
            'tenor' => ($pengajuan->tenor_pembayaran ?? 1) . ' Bulan',
            'biaya_admin' => 'Rp. ' . number_format($biayaAdmin, 0, ',', '.'),
            'biaya_admin_raw' => $biayaAdmin,
            'nisbah' => ($pengajuan->persentase_bagi_hasil ?? 2) . '% flat / bulan',
            'denda_keterlambatan' => '2% dari jumlah yang belum dibayarkan untuk periode pembayaran tersebut',
            'jaminan' => $pengajuan->jenis_pembiayaan ?? 'Invoice Financing',
            'tanda_tangan' => $pengajuan->debitur->tanda_tangan ?? null,
        ];
    }

    /**
     * Preview kontrak — redirect ke halaman preview.
     */
    public function previewKontrak()
    {
        $id = $this->pengajuan->id_pengajuan_peminjaman;

        return $this->redirect(
            route('peminjaman.preview-kontrak', $id),
            navigate: false
        );
    }

    /**
     * Download kontrak sebagai PDF menggunakan DomPDF.
     */
    public function downloadKontrak()
    {
        try {
            $kontrak = $this->prepareKontrakData();
            $html = $this->buildKontrakHTML($kontrak);

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $noKontrak = $kontrak['no_kontrak'] ?: 'DRAFT';
            $filename = 'Kontrak_Peminjaman_' . str_replace('/', '_', $noKontrak) . '_' . date('Ymd') . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating PDF kontrak: ' . $e->getMessage());

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Gagal membuat PDF kontrak. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Build custom HTML for PDF contract.
     * Duplikasi logika dari PeminjamanController untuk konsistensi output.
     */
    protected function buildKontrakHTML(array $kontrak): string
    {
        $ttdKreditur = public_path('assets/img/ttd2.png');
        $ttdKrediturBase64 = '';
        if (file_exists($ttdKreditur)) {
            $ttdKrediturBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($ttdKreditur));
        }

        $ttdDebitur = '';
        if (!empty($kontrak['tanda_tangan'])) {
            $ttdDebiturPath = storage_path('app/public/' . $kontrak['tanda_tangan']);
            if (file_exists($ttdDebiturPath)) {
                $ttdDebitur = 'data:image/png;base64,' . base64_encode(file_get_contents($ttdDebiturPath));
            }
        }

        $e = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');

        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kontrak Peminjaman</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }
        .title {
            text-align: center;
            margin: 30px 0;
        }
        .title h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        .title h3 {
            font-size: 14px;
            color: #0066cc;
            margin: 5px 0;
        }
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        .section {
            margin: 20px 0;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table td {
            padding: 5px 8px;
            vertical-align: top;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-row {
            display: table;
            width: 100%;
        }
        .signature-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signature-box {
            min-height: 100px;
            margin: 20px 0;
        }
        .signature-img {
            max-height: 60px;
            max-width: 150px;
            margin-bottom: 10px;
        }
        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 10px auto;
        }
        .fw-bold {
            font-weight: bold;
        }
        .text-muted {
            color: #666;
        }
        .mb-3 {
            margin-bottom: 15px;
        }
        .mb-4 {
            margin-bottom: 20px;
        }
        .mb-5 {
            margin-bottom: 25px;
        }
        .ps-3 {
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>' . $e($kontrak['nama_perusahaan']) . '</h2>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>FINANCING CONTRACT</h1>
        <h3>No: ' . $e($kontrak['no_kontrak2']) . '</h3>
    </div>

    <!-- Content -->
    <div class="content">
        <p class="mb-4">Yang bertandatangan dibawah ini:</p>

        <!-- Pihak Pertama -->
        <div class="section">
            <p class="fw-bold mb-3">I. ' . $e($kontrak['nama_perusahaan']) . '</p>
            <p>
                suatu perusahaan yang mengelola treasury serta memberikan pelayanan private equity, yang
                berkedudukan di Bandung, beralamat di PermataKuningan Building 17th Floor, Kawasan
                Epicentrum, HR Rasuna Said, Jl. Kuningan Mulia, RT.6/RW.1, Menteng Atas, Setiabudi, South
                Jakarta City, Jakarta12920 ("Kreditur") dalam hal ini diwakili oleh S-FINANCE berkedudukan
                di Jakarta sebagai Pengelola Fasilitas yang menyalurkan dan mengelola transaksi-transaksi
                terkait Fasilitas Pembiayaan yang bertindak sebagai kuasa (selanjutnya disebut "Perseroan"), dan
            </p>
        </div>

        <!-- Pihak Kedua -->
        <div class="section mb-5">
            <p class="fw-bold mb-3">II. Debitur, sebagaimana dimaksud dalam Struktur dan Kontrak Pembiayaan ini</p>
            <p>
                Dengan ini sepakat untuk menetapkan hal-hal pokok, yang selanjutnya akan disebut sebagai
                "Struktur dan Kontrak Pembiayaan" sehubungan dengan Perjanjian Pembiayaan Project Dengan
                Cara Pencairan Dengan Pembayaran Secara Angsuran atau Kontan ini (selanjutnya disebut
                sebagai "Perjanjian"), sebagai berikut:
            </p>
        </div>

        <!-- Data Pembiayaan -->
        <div class="section mb-5">
            <table>
                <tr>
                    <td width="5%">1.</td>
                    <td width="35%">Jenis Pembiayaan</td>
                    <td width="60%">: ' . $e($kontrak['jenis_pembiayaan']) . '</td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td class="fw-bold">Debitur</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">a. Nama Perusahaan</td>
                    <td>: ' . $e($kontrak['nama_debitur']) . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">b. Nama Pimpinan</td>
                    <td>: ' . $e($kontrak['nama_pimpinan']) . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">c. Alamat Perusahaan</td>
                    <td>: ' . $e($kontrak['alamat']) . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">d. Tujuan Pembiayaan</td>
                    <td>: ' . $e($kontrak['tujuan_pembiayaan']) . '</td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td class="fw-bold">Detail Pembiayaan</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">a. Nilai Pembiayaan</td>
                    <td class="fw-bold">: ' . $e($kontrak['nilai_pembiayaan']) . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="ps-3">b. Hutang Pokok</td>
                    <td class="fw-bold">: ' . $e($kontrak['hutang_pokok']) . '</td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td>Tenor Pembiayaan</td>
                    <td>: ' . $e($kontrak['tenor']) . '</td>
                </tr>
                <tr>
                    <td>5.</td>
                    <td>Biaya Administrasi</td>
                    <td>: ' . $e($kontrak['biaya_admin']) . '</td>
                </tr>
                <tr>
                    <td>6.</td>
                    <td>Bagi Hasil (Nisbah)</td>
                    <td>: ' . $e($kontrak['nisbah']) . '</td>
                </tr>
                <tr>
                    <td>7.</td>
                    <td>Denda Keterlambatan</td>
                    <td>: ' . $e($kontrak['denda_keterlambatan']) . '</td>
                </tr>
                <tr>
                    <td>8.</td>
                    <td>Jaminan</td>
                    <td>: ' . $e($kontrak['jaminan']) . '</td>
                </tr>
                <tr>
                    <td>9.</td>
                    <td>Metode Pembiayaan</td>
                    <td>: Transfer</td>
                </tr>
            </table>
        </div>

        <!-- Penutup -->
        <div class="section mb-5">
            <p class="fw-bold mb-3">Penutup</p>
            <p>
                "Bahwa dengan menerima pembiayaan tersebut bersamaan dengan tanda tangan kami, maka segala
                tanggung jawab pengembalian pembiayaan akan kami tepati sesuai dengan plan paid yang telah
                kami buat sendiri yang tertera pada tabel diatas. Apabila terdapat keterlambatan pembayaran
                kami bersedia untuk dikenakan denda penalti hingga sanksi tidak dapat mengakses pembiayaan
                apapun yang terafiliasi dengan S Finance sebelum tanggung jawab pelunasan hutang terlebih
                dahulu kami selesaikan"
            </p>
        </div>

        <!-- Tanggal -->
        <div class="section mb-5">
            <p class="text-muted">Jakarta, ' . $e($kontrak['tanggal_kontrak']) . '</p>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-row">
                <div class="signature-col">
                    <p class="fw-bold">Kreditur</p>
                    <p>' . $e($kontrak['nama_perusahaan']) . '</p>
                    <div class="signature-box">';

        if ($ttdKrediturBase64) {
            $html .= '<img src="' . $ttdKrediturBase64 . '" class="signature-img" />';
        }

        $html .= '
                        <div class="signature-line"></div>
                        <p class="fw-bold">Muhamad Kurniawan</p>
                    </div>
                    <p class="text-muted">Director</p>
                </div>
                <div class="signature-col">
                    <p class="fw-bold">Debitur</p>
                    <p>' . $e($kontrak['nama_pimpinan']) . '</p>
                    <div class="signature-box">';

        if ($ttdDebitur) {
            $html .= '<img src="' . $ttdDebitur . '" class="signature-img" />';
        }

        $html .= '
                        <div class="signature-line"></div>
                        <p class="fw-bold">' . $e($kontrak['nama_pimpinan']) . '</p>
                    </div>
                    <p class="text-muted">Pimpinan</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';

        return $html;
    }
}
