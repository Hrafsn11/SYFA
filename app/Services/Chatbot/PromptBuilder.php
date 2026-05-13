<?php

namespace App\Services\Chatbot;

use App\Models\PengajuanCicilan;
use App\Models\PengajuanInvestasi;
use App\Models\PengajuanPeminjaman;
use App\Models\PenyesuaianCicilan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

final class PromptBuilder
{
    public function build(User $user, string $message): string
    {
        $debitur = $user->relationLoaded('debitur') ? $user->debitur : $user->debitur()->first();
        $isAdmin = $user->hasAnyRole(['super-admin', 'admin']) || $debitur === null;
        $today = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY');
        $nama = $debitur?->nama ?? $user->name;
        $peran = $isAdmin ? 'Admin SYFA' : "Finance Officer — {$nama}";

        $base = $this->buildBasePrompt($user->name, $peran, $today);
        $modules = $this->buildModuleContext($message);

        $dataBlock = $isAdmin
            ? "\n[MODE ADMIN — tidak ada data pinjaman/investasi pribadi. Bantu pertanyaan umum SYFA.]"
            : $this->buildDynamicContext($debitur, $message);

        $formulaBlock = $this->needsSimulationGuide($message)
            ? "\nFORMAT TABEL WAJIB: |No|Pokok|Bunga|Cicilan|Sisa| — tampilkan SEMUA baris + baris Total di akhir."
            . " Jika rate tidak diketahui, gunakan estimasi 2%/bulan *(aktual ditetapkan Admin SYFA)*."
            : '';

        return $base . $modules . $dataBlock . $formulaBlock;
    }

    private function buildBasePrompt(string $userName, string $peran, string $today): string
    {
        return <<<PROMPT
Kamu adalah **SYFA Assistant**, asisten keuangan digital internal sistem SYFA (Captive Finance Grup Holding).
Hari ini: {$today} | Pengguna: **{$userName}** ({$peran})

ATURAN DASAR:
- Bahasa Indonesia formal. Sebut Finance Officer (bukan "kamu" atau "Anda").
- Bold angka rupiah, status penting, dan nama menu.
- Jawab ringkas, tabel boleh tampil penuh jika diminta.
- Tolak topik di luar konteks SYFA/keuangan.
- Tutup tiap jawaban dengan arahan spesifik ke menu atau langkah berikutnya di aplikasi ini.
- JANGAN mengarang data/angka — gunakan data CONTEXT DATA di bawah. Jika data tidak ada, katakan "silakan cek di menu …".
PROMPT;
    }

    private function buildModuleContext(string $message): string
    {
        $needsPinjaman = (bool) preg_match(
            '/pinjam|invoice|pembiayaan|pencairan|kontrak|status|jatuh.?tempo|denda|telat|macet|simulasi|flat|anuitas/i',
            $message
        );
        $needsCicilan = (bool) preg_match('/cicilan|angsuran|penyesuaian|restrukturisasi|jadwal|tenor/i', $message);
        $needsInvestasi = (bool) preg_match('/investasi|investor/i', $message);
        $needsPembayaran = (bool) preg_match('/pengembalian|tagihan|pembayaran|dpd|riwayat/i', $message);
        $needsAdmin = (bool) preg_match('/role|permission|akses|user|admin/i', $message);

        $sections = [$this->moduleOverview()];

        if ($needsPinjaman) {
            $sections[] = $this->peminjamanSection();
        }
        if ($needsPembayaran) {
            $sections[] = $this->pembayaranSection();
        }
        if ($needsCicilan) {
            $sections[] = $this->cicilanSection();
        }
        if ($needsInvestasi) {
            $sections[] = $this->investasiSection();
        }
        if ($needsAdmin) {
            $sections[] = $this->rolesSection();
        }

        if (count($sections) === 1) {
            $sections[] = $this->capabilitySection();
        }

        if ($this->needsSimulationGuide($message)) {
            $sections[] = $this->simulationGuide();
        }

        return "\n\n" . implode("\n\n", $sections) . "\n";
    }

    private function moduleOverview(): string
    {
        return <<<PROMPT
═══════════════════════════════════════════════════════════
ARSITEKTUR SISTEM SYFA
═══════════════════════════════════════════════════════════
Sistem SYFA terdiri dari 2 modul utama:
1. **SFinance** (/sfinance) — modul utama untuk Debitur dan Investor
PROMPT;
    }

    private function capabilitySection(): string
    {
        return <<<PROMPT
MODUL UTAMA — RINGKAS
- **Peminjaman Dana**: daftar pinjaman, status, detail kontrak, pengajuan baru
- **Penyesuaian Cicilan**: restrukturisasi, jadwal angsuran, simulasi
- **Investasi**: pengajuan investasi reguler/khusus, status kontrak
- **Pembayaran**: pengembalian dana, riwayat tagihan
PROMPT;
    }

    private function peminjamanSection(): string
    {
        return <<<PROMPT
MODUL PEMINJAMAN DANA (/peminjaman)
- Jenis pembiayaan: Invoice Financing, Installment
- Status: draft → pending → verifikasi → review → approved → disbursed → active → completed/paid
- Field penting: no_kontrak, jenis_pembiayaan, total_pinjaman, sisa_bayar_pokok, tanggal_jatuh_tempo, denda_keterlambatan
- Cara ajukan: **Peminjaman Dana** → **Ajukan Pinjaman Baru** → isi form → upload dokumen → Submit
PROMPT;
    }

    private function pembayaranSection(): string
    {
        return <<<PROMPT
MODUL PEMBAYARAN & TAGIHAN
- **Pengembalian Dana** (/pengembalian): upload bukti transfer, admin konfirmasi
- **Riwayat Tagihan** (/riwayat-tagihan): histori pembayaran per kontrak
- **Monitoring Pembayaran** (/monitoring-pembayaran): DPD, status pembayaran
PROMPT;
    }

    private function cicilanSection(): string
    {
        return <<<PROMPT
MODUL PENYESUAIAN CICILAN
- Pengajuan restrukturisasi: **Pengajuan Cicilan** → **Buat Pengajuan Baru**
- Metode perhitungan: **Flat** dan **Anuitas** (ditetapkan Admin)
- Cara cek jadwal: **Penyesuaian Cicilan** → klik kontrak → tab Jadwal Angsuran
PROMPT;
    }

    private function investasiSection(): string
    {
        return <<<PROMPT
MODUL INVESTASI (/pengajuan-investasi)
- Jenis: **Reguler** (aman) dan **Khusus** (bunga lebih tinggi)
- Status: draft → pending → approved → active → completed
- Dokumen: upload bukti transfer dana investasi
PROMPT;
    }

    private function rolesSection(): string
    {
        return <<<PROMPT
MANAJEMEN PENGGUNA & ROLES
- Role: super-admin, admin, finance-officer, investor
- Menu: /users, /roles, /permissions
PROMPT;
    }

    private function simulationGuide(): string
    {
        return <<<PROMPT
ALUR SIMULASI CICILAN — ikuti ketat
S1: Topik pinjaman/cicilan → tawarkan: Simulasi / Info Denda / Cara Ajukan
S2: Simulasi diminta → tanya jumlah pokok
S3: Jumlah masuk → tanya metode Flat atau Anuitas (jelaskan perbedaan 1 kalimat)
S4: Metode dipilih → tanya tenor. Jika user tulis "Flat 6 Bulan" → LANGSUNG hitung
S5: Tampilkan tabel |No|Pokok|Bunga|Cicilan|Sisa| + perbandingan Flat vs Anuitas → arahkan ke menu **Peminjaman Dana** atau **Pengajuan Cicilan**
S-DENDA: Tanya denda/DPD → hitung denda jika data tersedia → arahkan ke **Penyesuaian Cicilan**
RUMUS: FLAT: cicilan=(P+P×r×n)/n, r=rate%/12. ANUITAS: r=rate%/1200; cicilan=P×r×(1+r)^n/((1+r)^n - 1).
PROMPT;
    }

    private function needsSimulationGuide(string $message): bool
    {
        return (bool) preg_match('/simulasi|hitung|cicilan|flat|anuitas|tenor/i', $message);
    }

    /**
     * Fetch & format data keuangan user berdasarkan kata kunci pesan.
     */
    private function buildDynamicContext($debitur, string $message): string
    {
        $id = $debitur->id_debitur;

        $needsPinjaman = (bool) preg_match(
            '/pinjam|invoice|po.?financing|factoring|pencairan|kontrak|status|jatuh.?tempo|denda|telat|macet|gagal.?bayar|simulasi|flat|anuitas/i',
            $message
        );
        $needsCicilan = (bool) preg_match('/cicilan|angsuran|penyesuaian|restrukturisasi|jadwal/i', $message);
        $needsInvestasi = (bool) preg_match('/investasi|investor/i', $message);
        $isGreeting = !$needsPinjaman && !$needsCicilan && !$needsInvestasi;

        if ($isGreeting) {
            return $this->buildBriefSummary($debitur);
        }

        $ctx = '';

        if ($needsPinjaman) {
            $pinjaman = Cache::remember("chatbot_pja_{$id}", 45, fn () =>
                PengajuanPeminjaman::where('id_debitur', $id)
                    ->whereNotIn('status', ['draft', 'rejected', 'cancelled', 'completed', 'paid'])
                    ->latest()
                    ->get(['no_kontrak', 'jenis_pembiayaan', 'total_pinjaman', 'status', 'tanggal_jatuh_tempo', 'sisa_bayar_pokok'])
            );

            $alerts = '';
            foreach ($pinjaman as $p) {
                $jt = $p->tanggal_jatuh_tempo ? Carbon::parse($p->tanggal_jatuh_tempo) : null;
                $sisa = $jt ? (int) Carbon::now()->diffInDays($jt, false) : null;
                if ($sisa !== null && $sisa >= 0 && $sisa <= 5) {
                    $alerts .= "⚠️ {$p->no_kontrak} JT {$sisa} hari ({$jt->format('d/m/Y')})"
                        . " sisa Rp" . number_format((float) ($p->sisa_bayar_pokok ?? 0), 0, ',', '.') . "\n";
                }
            }
            if ($alerts) {
                $ctx .= "\n🚨 ALERT JATUH TEMPO — PROAKTIF: Buka respons dengan info ini & tawarkan simulasi:\n{$alerts}";
            }

            if ($pinjaman->isEmpty()) {
                $ctx .= "\n## Pinjaman Aktif\nTidak ada pinjaman aktif.\n";
            } else {
                $ctx .= "\n## Pinjaman Aktif\n";
                foreach ($pinjaman as $p) {
                    $jt = $p->tanggal_jatuh_tempo ? Carbon::parse($p->tanggal_jatuh_tempo) : null;
                    $sisa = $jt ? (int) Carbon::now()->diffInDays($jt, false) : null;
                    $ctx .= "- {$p->no_kontrak} | {$p->jenis_pembiayaan}"
                        . " | Rp" . number_format((float) ($p->total_pinjaman ?? 0), 0, ',', '.')
                        . " | {$p->status}"
                        . ($jt ? " | JT: {$jt->format('d/m/Y')} ({$sisa}hr)" : '')
                        . " | Sisa: Rp" . number_format((float) ($p->sisa_bayar_pokok ?? 0), 0, ',', '.') . "\n";
                }
            }

            $pending = Cache::remember("chatbot_pjp_{$id}", 45, fn () =>
                PengajuanPeminjaman::where('id_debitur', $id)
                    ->whereIn('status', ['draft', 'pending', 'review', 'waiting', 'verifikasi'])
                    ->latest()
                    ->get(['jenis_pembiayaan', 'total_pinjaman', 'status'])
            );

            if ($pending->isNotEmpty()) {
                $ctx .= "## Pending\n";
                foreach ($pending as $pp) {
                    $ctx .= "- {$pp->jenis_pembiayaan} Rp"
                        . number_format((float) ($pp->total_pinjaman ?? 0), 0, ',', '.') . " ({$pp->status})\n";
                }
            }
        }

        if ($needsCicilan) {
            $pengajuanCicilan = PengajuanCicilan::where('id_debitur', $debitur->id_debitur)
                ->latest()
                ->first();

            $ctx .= "\n## Penyesuaian Cicilan\n";
            if (!$pengajuanCicilan) {
                $ctx .= "Tidak ada pengajuan penyesuaian cicilan.\n";
            } else {
                $jenisArr = is_array($pengajuanCicilan->jenis_restrukturisasi)
                    ? implode(', ', array_filter($pengajuanCicilan->jenis_restrukturisasi))
                    : ($pengajuanCicilan->jenis_restrukturisasi ?? '-');

                $ctx .= "Kontrak: {$pengajuanCicilan->nomor_kontrak_pembiayaan}"
                    . " | Status: {$pengajuanCicilan->status}"
                    . " | Sisa Pokok: Rp" . number_format((float) ($pengajuanCicilan->sisa_pokok_belum_dibayar ?? 0), 0, ',', '.')
                    . " | Jenis: {$jenisArr}\n";

                $penyesuaian = PenyesuaianCicilan::where('id_pengajuan_cicilan', $pengajuanCicilan->id_pengajuan_cicilan)
                    ->whereIn('status', ['active', 'approved', 'running'])
                    ->with('jadwalAngsuran')
                    ->first();

                if ($penyesuaian) {
                    $next = $penyesuaian->jadwalAngsuran
                        ->where('status', '!=', 'paid')
                        ->sortBy('no')
                        ->first();
                    $ctx .= "Aktif: {$penyesuaian->metode_perhitungan}"
                        . " | Bunga: {$penyesuaian->suku_bunga_per_tahun}%/thn"
                        . " | Tenor: {$penyesuaian->jangka_waktu_total} bln\n";
                    if ($next) {
                        $ctx .= "Angsuran ke-{$next->no}: Rp"
                            . number_format((float) $next->total_cicilan, 0, ',', '.')
                            . " — " . Carbon::parse($next->tanggal_jatuh_tempo)->format('d/m/Y') . "\n";
                    }
                }
            }
        }

        if ($needsInvestasi) {
            $investasi = Cache::remember("chatbot_inv_{$id}", 45, fn () =>
                PengajuanInvestasi::where('id_debitur_dan_investor', $id)
                    ->whereNotIn('status', ['draft', 'rejected', 'cancelled'])
                    ->latest()
                    ->get(['nomor_kontrak', 'jenis_investasi', 'jumlah_investasi', 'bunga_pertahun', 'status'])
            );

            $ctx .= "\n## Investasi Aktif\n";
            if ($investasi->isEmpty()) {
                $ctx .= "Tidak ada investasi aktif.\n";
            } else {
                foreach ($investasi as $inv) {
                    $ctx .= "- {$inv->nomor_kontrak} | {$inv->jenis_investasi}"
                        . " | Rp" . number_format((float) ($inv->jumlah_investasi ?? 0), 0, ',', '.')
                        . " | {$inv->bunga_pertahun}%/thn | {$inv->status}\n";
                }
            }
        }

        return $ctx;
    }

    private function buildBriefSummary($debitur): string
    {
        $id = $debitur->id_debitur;

        $pinjamanCount = Cache::remember("chatbot_cnt_pj_{$id}", 45, fn () =>
            PengajuanPeminjaman::where('id_debitur', $id)
                ->whereNotIn('status', ['draft', 'rejected', 'cancelled', 'completed', 'paid'])
                ->count()
        );
        $investasiCount = Cache::remember("chatbot_cnt_inv_{$id}", 45, fn () =>
            PengajuanInvestasi::where('id_debitur_dan_investor', $id)
                ->whereNotIn('status', ['draft', 'rejected', 'cancelled'])
                ->count()
        );

        $ctx = "\n## Ringkasan Akun\n- Pinjaman aktif: {$pinjamanCount}\n- Investasi aktif: {$investasiCount}\n";

        $pinjaman = Cache::remember("chatbot_pja_brief_{$id}", 45, fn () =>
            PengajuanPeminjaman::where('id_debitur', $id)
                ->whereNotIn('status', ['draft', 'rejected', 'cancelled', 'completed', 'paid'])
                ->get(['no_kontrak', 'tanggal_jatuh_tempo', 'sisa_bayar_pokok'])
        );

        $alerts = '';
        foreach ($pinjaman as $p) {
            $jt = $p->tanggal_jatuh_tempo ? Carbon::parse($p->tanggal_jatuh_tempo) : null;
            $sisa = $jt ? (int) Carbon::now()->diffInDays($jt, false) : null;
            if ($sisa !== null && $sisa >= 0 && $sisa <= 5) {
                $alerts .= "⚠️ {$p->no_kontrak} jatuh tempo {$sisa} hari"
                    . " — sisa Rp" . number_format((float) ($p->sisa_bayar_pokok ?? 0), 0, ',', '.') . "\n";
            }
        }

        if ($alerts) {
            $ctx = "\n🚨 ALERT JATUH TEMPO — PROAKTIF: Awali dengan alert ini & tawarkan simulasi:\n{$alerts}" . $ctx;
        }

        return $ctx;
    }
}
