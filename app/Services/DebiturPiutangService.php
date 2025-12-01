<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class DebiturPiutangService
{
    private const CACHE_TTL = 1800; 
    private const CACHE_KEY_PREFIX = 'debitur_piutang';
    private const BAGI_HASIL_PERCENTAGE = 0.02; 
    
    private const STATUS_AKTIF = ['Aktif', 'Dana Sudah Dicairkan'];
    
    private const STATUS_COLORS = [
        'Aktif' => 'success',
        'Dana Sudah Dicairkan' => 'info',
        'Lunas' => 'primary',
        'Tertunda' => 'warning',
        'Ditolak' => 'danger',
    ];

    protected bool $cacheEnabled = false; // Disabled for real-time updates

    public function getDebiturPiutangData(
        int $perPage = 10, 
        ?string $search = null, 
        bool $useCache = false // Default to false for fresh data
    ): LengthAwarePaginator {
        if (!$this->cacheEnabled || !$useCache) {
            return $this->queryDebiturPiutang($perPage, $search);
        }

        $cacheKey = $this->buildCacheKey($perPage, $search);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, fn() => 
            $this->queryDebiturPiutang($perPage, $search)
        );
    }

    protected function queryDebiturPiutang(int $perPage, ?string $search): LengthAwarePaginator
    {
        $query = DB::table('bukti_peminjaman')
            ->select($this->getSelectColumns())
            ->leftJoin('pengajuan_peminjaman', 'bukti_peminjaman.id_pengajuan_peminjaman', '=', 'pengajuan_peminjaman.id_pengajuan_peminjaman')
            ->leftJoin('master_debitur_dan_investor', 'pengajuan_peminjaman.id_debitur', '=', 'master_debitur_dan_investor.id_debitur')
            ->leftJoin($this->getLatestPengembalianSubquery(), 'pengajuan_peminjaman.id_pengajuan_peminjaman', '=', 'pengembalian_pinjaman.id_pengajuan_peminjaman')
            ->whereIn('pengajuan_peminjaman.status', self::STATUS_AKTIF)
            ->where(fn($q) => $q->whereNotNull('bukti_peminjaman.no_invoice')
                              ->orWhereNotNull('bukti_peminjaman.no_kontrak'));

        if ($search) {
            $query->where(function($q) use ($search) {
                $searchPattern = "%{$search}%";
                $q->where('master_debitur_dan_investor.nama', 'like', $searchPattern)
                  ->orWhere('pengajuan_peminjaman.nomor_peminjaman', 'like', $searchPattern)
                  ->orWhere('bukti_peminjaman.no_invoice', 'like', $searchPattern)
                  ->orWhere('bukti_peminjaman.no_kontrak', 'like', $searchPattern);
            });
        }

        // Order by latest first for better UX
        $data = $query->orderBy('bukti_peminjaman.created_at', 'desc')
                      ->paginate($perPage);

        // Transform data efficiently
        $data->getCollection()->transform(fn($item) => $this->calculateFields($item));

        return $data;
    }

    protected function getSelectColumns(): array
    {
        return [
            'bukti_peminjaman.id_bukti_peminjaman',
            'bukti_peminjaman.no_invoice',
            'bukti_peminjaman.no_kontrak',
            'bukti_peminjaman.nama_client as objek_jaminan',
            'pengajuan_peminjaman.id_pengajuan_peminjaman',
            'pengajuan_peminjaman.nomor_peminjaman',
            'pengajuan_peminjaman.id_debitur',
            'pengajuan_peminjaman.harapan_tanggal_pencairan as tanggal_pengajuan',
            'pengajuan_peminjaman.total_pinjaman as nilai_diajukan',
            'pengajuan_peminjaman.status',
            'pengajuan_peminjaman.persentase_bagi_hasil',
            'master_debitur_dan_investor.nama as nama_debitur',
            'pengembalian_pinjaman.lama_pemakaian as masa_penggunaan',
            'pengembalian_pinjaman.sisa_bagi_hasil as kurang_bayar_bagi_hasil',
            'pengembalian_pinjaman.sisa_bayar_pokok as sisa_pokok',
            'pengembalian_pinjaman.ulid as id_pengembalian',
            DB::raw('(SELECT nominal_yang_disetujui FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND validasi_dokumen = "disetujui" ORDER BY created_at DESC LIMIT 1) as nilai_dicairkan'),
            DB::raw('(SELECT tanggal_pencairan FROM history_status_pengajuan_pinjaman WHERE id_pengajuan_peminjaman = pengajuan_peminjaman.id_pengajuan_peminjaman AND tanggal_pencairan IS NOT NULL ORDER BY created_at DESC LIMIT 1) as tanggal_pencairan'),
            DB::raw('(SELECT MAX(created_at) FROM report_pengembalian WHERE id_pengembalian = pengembalian_pinjaman.ulid) as tanggal_bayar_terakhir'),
            DB::raw('(SELECT SUM(nilai_total_pengembalian) FROM report_pengembalian WHERE id_pengembalian = pengembalian_pinjaman.ulid) as nilai_bayar_total'),
            DB::raw('(SELECT MAX(hari_keterlambatan) FROM report_pengembalian WHERE id_pengembalian = pengembalian_pinjaman.ulid) as telat_hari'),
        ];
    }

    protected function getLatestPengembalianSubquery()
    {
        return DB::raw('(
            SELECT p1.* 
            FROM pengembalian_pinjaman p1
            INNER JOIN (
                SELECT id_pengajuan_peminjaman, MAX(updated_at) as max_updated
                FROM pengembalian_pinjaman
                GROUP BY id_pengajuan_peminjaman
            ) p2 ON p1.id_pengajuan_peminjaman = p2.id_pengajuan_peminjaman 
                AND p1.updated_at = p2.max_updated
        ) as pengembalian_pinjaman');
    }

    protected function calculateFields(object $item): object
    {
        // Use nilai_dicairkan or fallback to nilai_diajukan
        $item->nilai_dicairkan = $item->nilai_dicairkan ?? $item->nilai_diajukan;
        
        $masa_penggunaan = (int)($item->masa_penggunaan ?? 0);
        $nilai_dicairkan = (float)($item->nilai_dicairkan ?? 0);
        
        // Calculate bagi hasil
        $item->total_bagi_hasil = $masa_penggunaan * self::BAGI_HASIL_PERCENTAGE * $nilai_dicairkan;
        $item->yang_harus_dibayar = $nilai_dicairkan + $item->total_bagi_hasil;
        
        // Calculate loan duration
        $item->lama_pinjaman_bulan = $item->tanggal_pencairan 
            ? Carbon::parse($item->tanggal_pencairan)->diffInMonths(now()) 
            : 0;
        
        // Calculate remaining balance
        $nilai_bayar_total = (float)($item->nilai_bayar_total ?? 0);
        $item->total_sisa_ar = max(0, $item->yang_harus_dibayar - $nilai_bayar_total);
        
        // Calculate pokok belum bayar
        $item->nilai_pokok_belum_bayar = max(0, $item->total_sisa_ar - ($item->kurang_bayar_bagi_hasil ?? 0));
        
        // Calculate monthly bagi hasil
        $persentase = ((float)($item->persentase_bagi_hasil ?? 0)) / 100;
        $item->bagi_hasil_perbulan = $persentase * $item->nilai_pokok_belum_bayar;
        
        // Set status color
        $item->status_color = self::STATUS_COLORS[$item->status] ?? 'secondary';

        return $item;
    }

    public function getHistoriPembayaran(string $idPengembalian, ?string $period = null): \Illuminate\Support\Collection
    {
        // Get nilai dicairkan first untuk perhitungan running balance
        $pengembalian = DB::table('pengembalian_pinjaman')
            ->join('pengajuan_peminjaman', 'pengembalian_pinjaman.id_pengajuan_peminjaman', '=', 'pengajuan_peminjaman.id_pengajuan_peminjaman')
            ->leftJoin(
                DB::raw('(SELECT id_pengajuan_peminjaman, nominal_yang_disetujui 
                          FROM history_status_pengajuan_pinjaman 
                          WHERE validasi_dokumen = "disetujui" 
                          ORDER BY created_at DESC 
                          LIMIT 1) as history'),
                'pengajuan_peminjaman.id_pengajuan_peminjaman',
                '=',
                'history.id_pengajuan_peminjaman'
            )
            ->where('pengembalian_pinjaman.ulid', $idPengembalian)
            ->select(
                DB::raw('COALESCE(history.nominal_yang_disetujui, pengajuan_peminjaman.total_pinjaman) as nilai_dicairkan')
            )
            ->first();

        if (!$pengembalian) {
            return collect();
        }

        // Get payment history
        $query = DB::table('report_pengembalian')
            ->select(
                'created_at as tanggal_bayar',
                'nilai_total_pengembalian as nilai_bayar',
                'hari_keterlambatan'
            )
            ->where('id_pengembalian', $idPengembalian);

        if ($period) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$period]);
        }

        $data = $query->orderBy('created_at', 'asc')->get();

        // Start with nilai_dicairkan as initial balance
        $sisaPokok = (float)$pengembalian->nilai_dicairkan;

        // Calculate running balance: subtract each payment from remaining balance
        return $data->map(function($item) use (&$sisaPokok) {
            // Payment reduces the balance
            $pembayaran = (float)$item->nilai_bayar;
            $sisaPokok -= $pembayaran;
            
            // Display payment and remaining balance
            $item->pembayaran_pokok = $pembayaran;
            $item->sisa_pokok_selanjutnya = max(0, $sisaPokok); // Remaining unpaid balance
            
            return $item;
        });
    }

    public function getSummaryData(string $idPengembalian, ?string $period = null): array
    {
        // Get nilai dicairkan dan total bagi hasil
        $pengembalian = DB::table('pengembalian_pinjaman')
            ->join('pengajuan_peminjaman', 'pengembalian_pinjaman.id_pengajuan_peminjaman', '=', 'pengajuan_peminjaman.id_pengajuan_peminjaman')
            ->leftJoin(
                DB::raw('(SELECT id_pengajuan_peminjaman, nominal_yang_disetujui 
                          FROM history_status_pengajuan_pinjaman 
                          WHERE validasi_dokumen = "disetujui" 
                          ORDER BY created_at DESC 
                          LIMIT 1) as history'),
                'pengajuan_peminjaman.id_pengajuan_peminjaman',
                '=',
                'history.id_pengajuan_peminjaman'
            )
            ->where('pengembalian_pinjaman.ulid', $idPengembalian)
            ->select(
                DB::raw('COALESCE(history.nominal_yang_disetujui, pengajuan_peminjaman.total_pinjaman) as nilai_dicairkan'),
                'pengembalian_pinjaman.lama_pemakaian',
                'pengembalian_pinjaman.sisa_bagi_hasil'
            )
            ->first();

        if (!$pengembalian) {
            return [
                'subtotal_sisa' => 0,
                'pokok' => 0,
                'sisa_bagi_hasil' => 0,
                'telat_hari' => 0,
            ];
        }

        $nilai_dicairkan = $pengembalian->nilai_dicairkan ?? 0;
        $total_bagi_hasil = ($pengembalian->lama_pemakaian ?? 0) * self::BAGI_HASIL_PERCENTAGE * $nilai_dicairkan;

        // Get total pembayaran
        $query = DB::table('report_pengembalian')
            ->where('id_pengembalian', $idPengembalian);

        if ($period) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$period]);
        }

        $reportData = $query->selectRaw('
            COALESCE(SUM(nilai_total_pengembalian), 0) as total_bayar,
            COALESCE(MAX(hari_keterlambatan), 0) as max_telat
        ')->first();

        $total_bayar = $reportData->total_bayar ?? 0;
        $pokok_sisa = $nilai_dicairkan - $total_bayar;
        $sisa_bagi_hasil = $pengembalian->sisa_bagi_hasil ?? 0;

        return [
            'subtotal_sisa' => $pokok_sisa + $sisa_bagi_hasil,
            'pokok' => max(0, $pokok_sisa),
            'sisa_bagi_hasil' => max(0, $sisa_bagi_hasil),
            'telat_hari' => $reportData->max_telat ?? 0,
        ];
    }

    public function clearCache(?string $debiturId = null): void
    {
        if ($debiturId) {
            Cache::forget($this->buildCacheKey(10, null));
            return;
        }

        for ($perPage = 10; $perPage <= 100; $perPage += 15) {
            Cache::forget($this->buildCacheKey($perPage, null));
        }
    }

    protected function buildCacheKey(int $perPage, ?string $search): string
    {
        $key = self::CACHE_KEY_PREFIX . "_{$perPage}";
        
        if ($search) {
            $key .= '_' . md5($search);
        }
        
        return $key;
    }
}
