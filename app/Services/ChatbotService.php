<?php

namespace App\Services;

use App\Models\User;
use App\Models\PengajuanPeminjaman;
use App\Models\PengajuanCicilan;
use App\Models\PenyesuaianCicilan;
use App\Models\PengajuanInvestasi;
use App\Models\JadwalAngsuran;
use App\Models\MasterDebiturDanInvestor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChatbotService
{
    protected string $apiKey;
    // gemini-2.5-flash via v1beta - terbukti bekerja dengan API key ini
    protected string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
    }

    /**
     * Kirim pesan ke Gemini dengan konteks data SYFA user.
     */
    public function chat(User $user, string $message, array $history = []): array
    {
        $systemPrompt = $this->buildSystemPrompt($user, $message);
        $contents     = $this->buildContents($history, $message);

        try {
            $response = Http::timeout(30)
                ->withHeaders(['X-goog-api-key' => $this->apiKey])
                ->post($this->apiUrl, [
                'systemInstruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                'contents'          => $contents,
                'generationConfig'  => [
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 800,
                ],
            ]);

            if ($response->failed()) {
                $status = $response->status();
                Log::error('Gemini API error', ['status' => $status, 'body' => $response->body()]);

                if ($status === 429) {
                    return [
                        'message'       => '⏳ Asisten sedang sibuk, silakan coba lagi dalam beberapa detik.',
                        'quick_replies' => ['💰 Cek Status Pinjaman', '🔄 Penyesuaian Cicilan', '📈 Info Investasi'],
                    ];
                }

                return $this->errorResponse();
            }

            $text = $response->json('candidates.0.content.parts.0.text', '');

            return [
                'message'      => $text ?: 'Maaf, saya tidak bisa menjawab saat ini.',
                'quick_replies' => $this->generateQuickReplies($message, $text),
            ];
        } catch (\Exception $e) {
            Log::error('Chatbot exception: ' . $e->getMessage());
            return $this->errorResponse();
        }
    }

    /**
     * Bangun system prompt dengan data lengkap user SYFA.
     */
    protected function buildSystemPrompt(User $user, string $message): string
    {
        $debitur        = $user->debitur()->with('kol')->first();
        $namaPerusahaan = $debitur?->nama ?? $user->name;
        $today          = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY');
        $isAdmin        = $user->hasAnyRole(['super-admin', 'admin']) || $debitur === null;

        // ─── Jika Admin/Super-Admin: tidak ada data per-perusahaan ──
        if ($isAdmin) {
            $userContext = <<<TXT
PERAN: Admin / Super Admin SYFA
(Admin tidak memiliki data pinjaman/investasi pribadi.
 Admin dapat mengelola dan memonitor seluruh data anak perusahaan di sistem.)
TXT;
            $pinjamanInfo = 'N/A (User adalah Admin)';
            $pendingInfo  = 'N/A (User adalah Admin)';
            $cicilanInfo  = 'N/A (User adalah Admin)';
            $jadwalInfo   = 'N/A (User adalah Admin)';
            $investasiInfo = 'N/A (User adalah Admin)';
        } else {
            $userContext = "PERAN: Finance Officer / Perwakilan Anak Perusahaan\nPerusahaan: {$namaPerusahaan}";

            // ─── Data Pinjaman Aktif ─────────────────────────────────
            $pinjaman = PengajuanPeminjaman::where('id_debitur', $debitur->id_debitur)
                ->whereNotIn('status', ['draft', 'rejected', 'cancelled', 'completed', 'paid'])
                ->latest()
                ->get();

            $pinjamanInfo = $pinjaman->isEmpty() ? 'Tidak ada pinjaman aktif.' : '';
            foreach ($pinjaman as $p) {
                $jatuhTempo = $p->tanggal_jatuh_tempo ? Carbon::parse($p->tanggal_jatuh_tempo) : null;
                $sisaHari   = $jatuhTempo ? (int) Carbon::now()->diffInDays($jatuhTempo, false) : null;
                $alert      = ($sisaHari !== null && $sisaHari <= 7 && $sisaHari >= 0) ? ' ⚠️ HAMPIR JATUH TEMPO!' : '';

                $pinjamanInfo .= "  - Nomor Kontrak: " . ($p->no_kontrak ?? '-')
                    . ", Tipe: " . ($p->jenis_pembiayaan ?? '-')
                    . ", Jumlah: Rp " . number_format((float) ($p->total_pinjaman ?? 0), 0, ',', '.')
                    . ", Status: " . ($p->status ?? '-')
                    . ($jatuhTempo ? ", Jatuh Tempo: {$jatuhTempo->format('d M Y')}" : "")
                    . ($sisaHari !== null ? " (sisa {$sisaHari} hari){$alert}" : "")
                    . ", Sisa Pokok: Rp " . number_format((float) ($p->sisa_bayar_pokok ?? 0), 0, ',', '.')
                    . "\n";
            }

            // ─── Pengajuan Pinjaman Pending ──────────────────────────
            $pengajuanPending = PengajuanPeminjaman::where('id_debitur', $debitur->id_debitur)
                ->whereIn('status', ['draft', 'pending', 'review', 'waiting', 'verifikasi'])
                ->latest()
                ->get();

            $pendingInfo = $pengajuanPending->isEmpty() ? 'Tidak ada pengajuan pending.' : '';
            foreach ($pengajuanPending as $pp) {
                $pendingInfo .= "  - Tipe: " . ($pp->jenis_pembiayaan ?? '-')
                    . ", Jumlah: Rp " . number_format((float) ($pp->total_pinjaman ?? 0), 0, ',', '.')
                    . ", Status: " . ($pp->status ?? '-')
                    . ", Diajukan: " . ($pp->created_at ? $pp->created_at->format('d M Y') : '-')
                    . "\n";
            }

            // ─── Pengajuan Cicilan ───────────────────────────────────
            $pengajuanCicilan = PengajuanCicilan::where('id_debitur', $debitur->id_debitur)
                ->latest()
                ->first();

            $cicilanInfo = 'Tidak ada pengajuan penyesuaian cicilan.';
            if ($pengajuanCicilan) {
                $jenisArr = is_array($pengajuanCicilan->jenis_restrukturisasi)
                    ? implode(', ', array_filter($pengajuanCicilan->jenis_restrukturisasi))
                    : ($pengajuanCicilan->jenis_restrukturisasi ?? '-');

                $cicilanInfo = "Nomor Kontrak: " . ($pengajuanCicilan->nomor_kontrak_pembiayaan ?? '-')
                    . ", Status: " . ($pengajuanCicilan->status ?? '-')
                    . ", Sisa Pokok: Rp " . number_format((float) ($pengajuanCicilan->sisa_pokok_belum_dibayar ?? 0), 0, ',', '.')
                    . ", Jenis: {$jenisArr}";
            }

            // ─── Penyesuaian Cicilan Aktif ───────────────────────────
            $penyesuaianAktif = $pengajuanCicilan
                ? PenyesuaianCicilan::where('id_pengajuan_cicilan', $pengajuanCicilan->id_pengajuan_cicilan)
                    ->whereIn('status', ['active', 'approved', 'running'])
                    ->with('jadwalAngsuran')
                    ->first()
                : null;

            $jadwalInfo = 'Tidak ada jadwal cicilan aktif.';
            if ($penyesuaianAktif) {
                $angsuranBerikutnya = $penyesuaianAktif->jadwalAngsuran
                    ->where('status', '!=', 'paid')
                    ->sortBy('no')
                    ->first();

                $jadwalInfo = "Metode: " . ($penyesuaianAktif->metode_perhitungan ?? '-')
                    . ", Bunga: " . ($penyesuaianAktif->suku_bunga_per_tahun ?? 0) . "%/tahun"
                    . ", Tenor: " . ($penyesuaianAktif->jangka_waktu_total ?? 0) . " bulan"
                    . ", Total Cicilan: Rp " . number_format((float) ($penyesuaianAktif->total_cicilan ?? 0), 0, ',', '.');

                if ($angsuranBerikutnya) {
                    $jadwalInfo .= "\n  Angsuran ke-{$angsuranBerikutnya->no} (berikutnya): "
                        . "Rp " . number_format((float) $angsuranBerikutnya->total_cicilan, 0, ',', '.')
                        . " jatuh tempo " . Carbon::parse($angsuranBerikutnya->tanggal_jatuh_tempo)->format('d M Y');
                }
            }

            // ─── Investasi Aktif ─────────────────────────────────────
            $investasi = PengajuanInvestasi::where('id_debitur_dan_investor', $debitur->id_debitur)
                ->whereNotIn('status', ['draft', 'rejected', 'cancelled'])
                ->latest()
                ->get();

            $investasiInfo = $investasi->isEmpty() ? 'Tidak ada investasi aktif.' : '';
            foreach ($investasi as $inv) {
                $investasiInfo .= "  - Nomor Kontrak: " . ($inv->nomor_kontrak ?? '-')
                    . ", Jenis: " . ($inv->jenis_investasi ?? '-')
                    . ", Jumlah: Rp " . number_format((float) ($inv->jumlah_investasi ?? 0), 0, ',', '.')
                    . ", Bunga: " . ($inv->bunga_pertahun ?? 0) . "%/tahun"
                    . ", Status: " . ($inv->status ?? '-')
                    . "\n";
            }
        }

        // ─── Build Prompt ────────────────────────────────────────────
        return <<<PROMPT
Kamu adalah SYFA Assistant, asisten keuangan platform SYFA (Captive Finance Internal grup holding).
Hari ini: {$today}. Pengguna: {$user->name}. {$userContext}

DATA USER:
Pinjaman aktif: {$pinjamanInfo}
Pengajuan pending: {$pendingInfo}
Penyesuaian cicilan: {$cicilanInfo}
Jadwal cicilan: {$jadwalInfo}
Investasi: {$investasiInfo}

PRODUK SYFA (ringkas):
- PINJAMAN: Tipe=Invoice Financing/PO Financing/Factoring/Installment. Tenor FIXED 30 hari. Proses: Pengajuan→Verifikasi Dokumen→Approval→Pencairan. Dokumen: KTP direksi, NPWP, Akta, Rek.koran 3bln, Lapkeu, Invoice/PO sesuai tipe.
- PENYESUAIAN CICILAN: Jika tidak mampu lunasi pinjaman 30 hari. Proses: Form alasan→Upload dok→Approval SYFA→Hitung skema baru. Metode: Flat (cicilan tetap) atau Anuitas (bunga menurun). Tenor & bunga ditentukan Admin SYFA.
- INVESTASI: Reguler=bunga standar, Khusus=bunga lebih tinggi (ditentukan saat daftar). Proses: Pengajuan→Approval→Penyaluran→Pengembalian.

ATURAN:
- Bahasa Indonesia, ramah, ringkas (max 3 paragraf)
- Gunakan data aktual user di atas jika relevan
- Lakukan kalkulasi nyata jika diminta simulasi cicilan (flat/anuitas)
- Jangan buat keputusan approval, hanya info & simulasi
- Format rupiah: Rp 1.000.000
- Tolak pertanyaan di luar keuangan SYFA
PROMPT;
    }

    /**
     * Bangun array contents untuk Gemini (multi-turn conversation).
     */
    protected function buildContents(array $history, string $newMessage): array
    {
        $contents = [];

        foreach ($history as $item) {
            if (!empty($item['role']) && !empty($item['content'])) {
                $contents[] = [
                    'role'  => $item['role'] === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $item['content']]],
                ];
            }
        }

        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $newMessage]],
        ];

        return $contents;
    }

    /**
     * Generate quick reply buttons berdasarkan konteks percakapan.
     */
    protected function generateQuickReplies(string $userMessage, string $botResponse): array
    {
        $lower = strtolower($userMessage . ' ' . $botResponse);

        // Intent: Pinjaman
        if (str_contains($lower, 'pinjam') || str_contains($lower, 'invoice') || str_contains($lower, 'factoring') || str_contains($lower, 'installment')) {
            return [
                '📋 Syarat Pinjaman',
                '🧮 Simulasi Pinjaman',
                '📅 Cek Jadwal Jatuh Tempo',
                '🔄 Ajukan Penyesuaian Cicilan',
            ];
        }

        // Intent: Cicilan / Penyesuaian
        if (str_contains($lower, 'cicilan') || str_contains($lower, 'penyesuaian') || str_contains($lower, 'flat') || str_contains($lower, 'anuitas')) {
            return [
                '📊 Simulasi Flat',
                '📈 Simulasi Anuitas',
                '⚖️ Bandingkan Flat vs Anuitas',
                '📋 Cara Ajukan Penyesuaian',
            ];
        }

        // Intent: Investasi
        if (str_contains($lower, 'investasi') || str_contains($lower, 'reguler') || str_contains($lower, 'khusus') || str_contains($lower, 'bunga')) {
            return [
                '📦 Info Investasi Reguler',
                '⭐ Info Investasi Khusus',
                '🔄 Bandingkan Reguler vs Khusus',
                '📝 Cara Daftar Investasi',
            ];
        }

        // Intent: Status
        if (str_contains($lower, 'status') || str_contains($lower, 'pengajuan') || str_contains($lower, 'proses')) {
            return [
                '💼 Status Pinjaman',
                '📈 Status Investasi',
                '🔄 Status Penyesuaian Cicilan',
                '📊 Rekap Dashboard',
            ];
        }

        // Default quick replies
        return [
            '💰 Cek Status Pinjaman',
            '🔄 Penyesuaian Cicilan',
            '📈 Info Investasi',
            '📅 Cek Jatuh Tempo',
        ];
    }

    /**
     * Respon error standar.
     */
    protected function errorResponse(): array
    {
        return [
            'message'      => 'Maaf, terjadi gangguan koneksi. Silakan coba lagi dalam beberapa saat.',
            'quick_replies' => [
                '💰 Cek Status Pinjaman',
                '🔄 Penyesuaian Cicilan',
                '📈 Info Investasi',
            ],
        ];
    }
}
