<?php

namespace App\Services;

use App\Models\User;
use App\Models\PengajuanPeminjaman;
use App\Models\PengajuanCicilan;
use App\Models\PenyesuaianCicilan;
use App\Models\PengajuanInvestasi;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChatbotService
{
    protected string $apiUrl;
    protected string $model;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = 'https://integrate.api.nvidia.com/v1/chat/completions';
        $this->model  = config('services.nvidia.model', 'openai/gpt-oss-120b');
        $this->apiKey = config('services.nvidia.api_key', '');
    }

    /**
     * Kirim pesan ke NVIDIA NIM dengan konteks data SYFA user.
     */
    public function chat(User $user, string $message, array $history = []): array
    {
        $systemPrompt = $this->buildSystemPrompt($user, $message);
        $messages     = $this->buildMessages($systemPrompt, $history, $message);

        try {
            $headers = [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ];

            $payload = [
                'model'             => $this->model,
                'messages'          => $messages,
                'temperature'       => 1,
                'max_tokens'        => 1536,
                'top_p'             => 1,
                'frequency_penalty' => 0.0,
                'presence_penalty'  => 0.0,
            ];

            $response = Http::timeout(120)
                ->withHeaders($headers)
                ->post($this->apiUrl, $payload);

            if ($response->failed()) {
                $status = $response->status();
                Log::error('NVIDIA NIM API error', ['status' => $status, 'body' => $response->body()]);

                if ($status === 429) {
                    return [
                        'message'       => '⏳ Asisten sedang sibuk, silakan coba lagi dalam beberapa detik.',
                        'quick_replies' => ['💰 Cek Status Pinjaman', '🔄 Penyesuaian Cicilan', '📈 Info Investasi'],
                    ];
                }

                return $this->errorResponse();
            }

            $text = $response->json('choices.0.message.content', '');

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
     * Stream response dari NVIDIA NIM token per token.
     * $onToken dipanggil setiap ada token baru; return full text setelah selesai.
     */
    public function chatStream(User $user, string $message, array $history, callable $onToken): string
    {
        $systemPrompt = $this->buildSystemPrompt($user, $message);
        $messages     = $this->buildMessages($systemPrompt, $history, $message);

        $payload = json_encode([
            'model'             => $this->model,
            'messages'          => $messages,
            'temperature'       => 1,
            'max_tokens'        => 1536,
            'top_p'             => 1,
            'frequency_penalty' => 0.0,
            'presence_penalty'  => 0.0,
            'stream'            => true,
        ]);

        $fullText = '';
        $buffer   = '';

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_WRITEFUNCTION  => function ($ch, $data) use (&$fullText, &$buffer, $onToken) {
                $buffer .= $data;
                $lines   = explode("\n", $buffer);
                $buffer  = array_pop($lines); // simpan baris belum lengkap

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!str_starts_with($line, 'data: ')) continue;
                    $json = substr($line, 6);
                    if ($json === '[DONE]') continue;
                    $chunk = json_decode($json, true);
                    $token = $chunk['choices'][0]['delta']['content'] ?? '';
                    if ($token !== '') {
                        $fullText .= $token;
                        $onToken($token);
                    }
                }
                return strlen($data);
            },
        ]);

        curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('NVIDIA NIM stream cURL error', ['error' => curl_error($ch)]);
        }
        curl_close($ch);

        return $fullText;
    }

    /**
     * Bangun system prompt: base statis (lean) + dynamic context (keyword-filtered).
     */
    protected function buildSystemPrompt(User $user, string $message): string
    {
        $debitur = $user->debitur()->first();
        $isAdmin = $user->hasAnyRole(['super-admin', 'admin']) || $debitur === null;
        $today   = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY');
        $nama    = $debitur?->nama ?? $user->name;
        $peran   = $isAdmin ? 'Admin SYFA' : "Finance Officer — {$nama}";

        // ── 1. BASE SYSTEM ROLE (statis, selalu dikirim, ~180 token) ─────────
        $maxResp = 'Jawab selengkap yang diperlukan; tabel boleh penuh semua baris.';

        $base = <<<PROMPT
        Kamu adalah **SYFA Assistant**, asisten keuangan digital internal sistem SYFA (Captive Finance Grup Holding).
        Hari ini: {$today} | Pengguna: **{$user->name}** ({$peran})

        ATURAN DASAR:
        - Bahasa Indonesia formal. Sebut Finance Officer (bukan "kamu" atau "Anda").
        - Bold angka rupiah, status penting, dan nama menu.
        - {$maxResp}
        - Tolak topik di luar konteks SYFA/keuangan.
        - Tutup tiap jawaban dengan arahan spesifik ke menu atau langkah berikutnya di aplikasi ini.
        - JANGAN mengarang data/angka — gunakan data CONTEXT DATA di bawah. Jika data tidak ada, katakan "silakan cek di menu …".

        ═══════════════════════════════════════════════════════════
        ARSITEKTUR SISTEM SYFA
        ═══════════════════════════════════════════════════════════
        Sistem SYFA terdiri dari 2 modul utama yang terpisah:
        1. **SFinance** (/sfinance) — modul utama untuk Debitur (peminjam) dan Investor

        ═══════════════════════════════════════════════════════════
        MODUL 1: SFINANCE — DEBITUR & INVESTOR
        ═══════════════════════════════════════════════════════════

        ── A. PEMINJAMAN DANA (/peminjaman) ──────────────────────
        Fitur: Daftar semua pengajuan pinjaman, buat baru, lihat detail, edit, download kontrak PDF.
        Jenis pembiayaan:
          • Invoice Financing — berbasis invoice/faktur dari buyer ke pemasok
          • Installment — pinjaman modal kerja dengan cicilan tetap
        Tenor: standar 30 hari (bisa berbeda tergantung persetujuan Admin)
        Field penting: no_kontrak, jenis_pembiayaan, total_pinjaman, sisa_bayar_pokok, status,
          tanggal_jatuh_tempo, persentase_bunga, tenor_pembayaran, yang_harus_dibayarkan,
          denda_keterlambatan, jumlah_bulan_keterlambatan
        Alur status pinjaman (berurutan):
          draft → pending → verifikasi → review → approved → disbursed → active → completed/paid
          Status gagal: rejected, cancelled
        Alur persetujuan (multi-step):
          Step 1: Finance Officer submit → status "pending"
          Step 2: Admin SFinance verifikasi dokumen → status "verifikasi"
          Step 3: CEO SKI review → status "review"
          Step 4: Jika nominal ≥ Rp 300.000.000 → Direktur SKI wajib approve
          Step 5: Approved → pencairan dana → status "disbursed" → "active"
        Dokumen wajib upload di form (PDF/JPG):
          KTP Direksi, NPWP Perusahaan, Akta Pendirian + Perubahan, Rekening Koran 3 bulan, Laporan Keuangan
        Cara ajukan: klik **Peminjaman Dana** di sidebar → tombol **"Ajukan Pinjaman Baru"** → isi form → upload dokumen → Submit
        Cara lihat detail: klik no_kontrak di tabel → halaman detail dengan status real-time & history

        ── B. LAPORAN TAGIHAN BULANAN (/laporan-tagihan-bulanan) ──
        Fitur: AR (Account Receivable) per bulan — ringkasan tagihan jatuh tempo, sudah dibayar, dan outstanding.
        Berguna untuk: melihat total tagihan bulan ini, rekap per debitur, update data AR (edit via Admin).

        ── C. MONITORING PEMBAYARAN (/monitoring-pembayaran) ─────
        Fitur: AR Performance — tracking pembayaran real-time, filter per periode/status, export PDF.
        Kolom: no kontrak, debitur, total pinjaman, tanggal JT, status pembayaran, DPD (Days Past Due).
        DPD = jumlah hari keterlambatan pembayaran sejak jatuh tempo.

        ── D. PENGAJUAN CICILAN — RESTRUKTURISASI (/pengajuan-cicilan) ──
        Fitur: Finance Officer mengajukan restrukturisasi utang jika kesulitan bayar.
        Jenis restrukturisasi (bisa pilih lebih dari satu):
          • Perpanjangan tenor
          • Pengurangan margin/bunga
          • Grace period (masa tenggang tanpa cicilan)
          • Konversi ke penyesuaian cicilan
        Dokumen wajib: KTP PIC, NPWP Perusahaan, Laporan Keuangan, Arus Kas, Kontrak Pembiayaan, Tanda Tangan
        Status pengajuan cicilan: draft → submitted → review → committee_review → approved / rejected
        Field penting: nomor_kontrak_pembiayaan, sisa_pokok_belum_dibayar, jenis_restrukturisasi,
          alasan_restrukturisasi, status_dpd, current_step
        Cara ajukan: klik **Pengajuan Cicilan** → tombol "Buat Pengajuan Baru" → pilih kontrak pinjaman → isi form → upload dokumen

        ── E. PENYESUAIAN CICILAN (/penyesuaian-cicilan) ─────────
        Fitur: Setelah pengajuan cicilan disetujui Komite, Admin membuat jadwal angsuran baru.
        Dua metode perhitungan (ditetapkan Admin, bukan Finance Officer):
          • **Flat** — pokok cicilan tetap tiap bulan, bunga dihitung dari saldo awal
            Formula: cicilan = (P + P×r×n) / n, dengan r = rate%/12
          • **Anuitas** — total cicilan tetap tiap bulan, bunga menurun (porsi pokok makin besar)
            Formula: r = rate%/1200; cicilan = P×r×(1+r)^n / ((1+r)^n - 1)
        Field penting: metode_perhitungan, plafon_pembiayaan, suku_bunga_per_tahun, jangka_waktu_total,
          total_pokok, total_margin, total_cicilan, total_terbayar, masa_tenggang, tanggal_mulai_cicilan
        Status: pending → active / approved / running → completed
        Cara cek jadwal angsuran: klik **Penyesuaian Cicilan** → klik baris kontrak → tab "Jadwal Angsuran"

        ── F. PENGEMBALIAN DANA (/pengembalian) ─────────────────
        Fitur: Pencatatan pembayaran balik dari Debitur ke SYFA (pelunasan pinjaman).
        Finance Officer bisa upload bukti transfer, Admin konfirmasi penerimaan.
        Cara: klik **Pengembalian Dana** → tombol "Tambah Pengembalian" → pilih kontrak → isi nominal → upload bukti

        ── G. RIWAYAT TAGIHAN (/riwayat-tagihan) ─────────────────
        Fitur: Histori semua pembayaran yang pernah dilakukan, per kontrak, bisa filter tanggal.
        Berguna untuk: cek apakah tagihan sudah lunas, rekap pembayaran bulan lalu.

        ── H. LAPORAN PENGEMBALIAN (/laporan-pengembalian) ───────
        Fitur: Report ringkasan pengembalian dana, bisa export PDF.

        ── I. PEMINJAMAN INVESTASI (/pengajuan-investasi) ─────────
        Fitur untuk Investor: pengajuan investasi dana ke SYFA, Admin review & approve, generate kontrak PDF.
        Jenis investasi:
          • **Reguler** — bunga standar (lebih rendah), lebih aman, tenor fleksibel
          • **Khusus** — bunga lebih tinggi, syarat tertentu ditentukan Admin
        Field penting: jenis_investasi, jumlah_investasi, bunga_pertahun, lama_investasi,
          nominal_bunga_yang_didapatkan, sisa_pokok, sisa_bunga, nomor_kontrak, status
        Status investasi: draft → pending → approved → active → completed
        Alur: Investor submit → Admin approve → penyaluran dana → active → jatuh tempo → pengembalian
        Dokumen: upload bukti transfer dana investasi

        ── J. DASHBOARD CICILAN RESTRUKTURISASI ─────────────────
        Fitur: Overview semua restrukturisasi aktif — grafik progress, jadwal angsuran, DPD cicilan.

      

        ═══════════════════════════════════════════════════════════
        MASTER DATA
        ═══════════════════════════════════════════════════════════
        - **Master Debitur & Investor** (/master-data/debitur-investor): data perusahaan (nama, NPWP, rekening, CEO, Direktur, Komisaris). Status: active/inactive. Flagging: "tidak"=Debitur, "ya"=Investor.
    

        ═══════════════════════════════════════════════════════════
        MANAJEMEN PENGGUNA & ROLES
        ═══════════════════════════════════════════════════════════
        Role yang ada: super-admin, admin, finance-officer, investor
        - **Super Admin / Admin**: akses penuh — approve/reject, edit data, lihat semua debitur
        - **Finance Officer**: hanya akses data milik perusahaannya sendiri
        - **Investor**: hanya akses menu investasi
        Menu management: /users (kelola user), /roles (kelola role), /permissions (kelola akses)

        ═══════════════════════════════════════════════════════════
        ALUR SIMULASI CICILAN — ikuti ketat
        ═══════════════════════════════════════════════════════════
        S1: Topik pinjaman/cicilan → tawarkan: Simulasi / Info Denda / Cara Ajukan
        S2: Simulasi diminta → tanya jumlah pokok
        S3: Jumlah masuk → tanya metode Flat atau Anuitas (jelaskan perbedaan 1 kalimat)
        S4: Metode dipilih → tanya tenor. Jika user tulis "Flat 6 Bulan" → LANGSUNG hitung
        S5: Tampilkan tabel |No|Pokok|Bunga|Cicilan|Sisa| + perbandingan Flat vs Anuitas → arahkan ke menu **Peminjaman Dana** atau **Pengajuan Cicilan**
        S-DENDA: Tanya denda/DPD → hitung denda jika data tersedia → arahkan ke **Penyesuaian Cicilan**
        RUMUS: FLAT: cicilan=(P+P×r×n)/n, r=rate%/12. ANUITAS: r=rate%/1200; cicilan=P×r×(1+r)^n/((1+r)^n-1).
        Jika rate tidak ada, pakai estimasi **2% per bulan** *(aktual ditetapkan Admin SYFA)*.
        PROMPT;

        // ── 2. DYNAMIC DATA (hanya fetch + inject berdasarkan topik pesan) ─────
        $dataBlock = $isAdmin
            ? "\n[MODE ADMIN — tidak ada data pinjaman/investasi pribadi. Bantu pertanyaan umum SYFA.]"
            : $this->buildDynamicContext($debitur, $message);

        // ── 3. RUMUS (inject tambahan saat simulasi — contoh tabel) ─────────────
        $formulaBlock = '';
        if (preg_match('/simulasi|hitung|cicilan|flat|anuitas|tenor/i', $message)) {
            $formulaBlock = "\nFORMAT TABEL WAJIB: |No|Pokok|Bunga|Cicilan|Sisa| — tampilkan SEMUA baris + baris Total di akhir."
                . " Jika rate tidak diketahui, gunakan estimasi 2%/bulan *(aktual ditetapkan Admin SYFA)*.";
        }

        return $base . $dataBlock . $formulaBlock;
    }

    /**
     * Fetch & format data keuangan user berdasarkan kata kunci pesan.
     * Hanya query yang relevan yang dijalankan.
     */
    protected function buildDynamicContext($debitur, string $message): string
    {
        $id = $debitur->id_debitur;

        $needsPinjaman  = (bool) preg_match(
            '/pinjam|invoice|po.?financing|factoring|pencairan|kontrak|status|jatuh.?tempo|denda|telat|macet|gagal.?bayar|simulasi|flat|anuitas/i',
            $message
        );
        $needsCicilan   = (bool) preg_match('/cicilan|angsuran|penyesuaian|restrukturisasi|jadwal/i', $message);
        $needsInvestasi = (bool) preg_match('/investasi|investor/i', $message);
        $isGreeting     = !$needsPinjaman && !$needsCicilan && !$needsInvestasi;

        // Greeting atau pesan pendek → hanya ringkasan singkat + cek alert urgent
        if ($isGreeting) {
            return $this->buildBriefSummary($debitur);
        }

        $ctx = '';

        // ── PINJAMAN ────────────────────────────────────────────────────────
        if ($needsPinjaman) {
            $pinjaman = Cache::remember("chatbot_pja_{$id}", 45, fn () =>
                PengajuanPeminjaman::where('id_debitur', $id)
                    ->whereNotIn('status', ['draft', 'rejected', 'cancelled', 'completed', 'paid'])
                    ->latest()
                    ->get(['no_kontrak', 'jenis_pembiayaan', 'total_pinjaman', 'status',
                           'tanggal_jatuh_tempo', 'sisa_bayar_pokok'])
            );

            // Proactive alert
            $alerts = '';
            foreach ($pinjaman as $p) {
                $jt   = $p->tanggal_jatuh_tempo ? Carbon::parse($p->tanggal_jatuh_tempo) : null;
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
                    $jt   = $p->tanggal_jatuh_tempo ? Carbon::parse($p->tanggal_jatuh_tempo) : null;
                    $sisa = $jt ? (int) Carbon::now()->diffInDays($jt, false) : null;
                    $ctx .= "- {$p->no_kontrak} | {$p->jenis_pembiayaan}"
                        . " | Rp" . number_format((float) ($p->total_pinjaman ?? 0), 0, ',', '.')
                        . " | {$p->status}"
                        . ($jt ? " | JT: {$jt->format('d/m/Y')} ({$sisa}hr)" : '')
                        . " | Sisa: Rp" . number_format((float) ($p->sisa_bayar_pokok ?? 0), 0, ',', '.') . "\n";
                }
            }

            // Pengajuan pending (ringkas)
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

        // ── CICILAN ─────────────────────────────────────────────────────────
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

        // ── INVESTASI ────────────────────────────────────────────────────────
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

    /**
     * Ringkasan singkat untuk salam/pesan pendek: hanya jumlah + alert urgent.
     * Hemat token: tidak query detail, hanya COUNT + cek jatuh tempo.
     */
    protected function buildBriefSummary($debitur): string
    {
        $id = $debitur->id_debitur;

        $pinjamanCount  = Cache::remember("chatbot_cnt_pj_{$id}", 45, fn () =>
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

        // Cek alert urgent (hanya select kolom minimal)
        $pinjaman = Cache::remember("chatbot_pja_brief_{$id}", 45, fn () =>
            PengajuanPeminjaman::where('id_debitur', $id)
                ->whereNotIn('status', ['draft', 'rejected', 'cancelled', 'completed', 'paid'])
                ->get(['no_kontrak', 'tanggal_jatuh_tempo', 'sisa_bayar_pokok'])
        );

        $alerts = '';
        foreach ($pinjaman as $p) {
            $jt   = $p->tanggal_jatuh_tempo ? Carbon::parse($p->tanggal_jatuh_tempo) : null;
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

    /**
     * Bangun array messages untuk NVIDIA NIM / OpenAI-compatible API (multi-turn conversation).
     */
    protected function buildMessages(string $systemPrompt, array $history, string $newMessage): array
    {
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($history as $item) {
            if (!empty($item['role']) && !empty($item['content'])) {
                // Bersihkan artefak truncation lama agar tidak ikut dikirim ke LLM
                $content = str_replace('…[ringkasan tersimpan]', '', $item['content']);
                $content = rtrim($content);
                if ($content === '') continue;

                $messages[] = [
                    'role'    => $item['role'] === 'user' ? 'user' : 'assistant',
                    'content' => $content,
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $newMessage];

        return $messages;
    }

    /**
     * Generate quick reply buttons berdasarkan konteks percakapan.
     * Flow simulasi cicilan: Simulasi → Pilih Metode → Pilih Tenor → Hasil
     */
    public function generateQuickReplies(string $userMessage, string $botResponse): array
    {
        $u = strtolower($userMessage);
        $b = strtolower($botResponse);
        $all = $u . ' ' . $b;

        // ═══════════════════════════════════════════════════════════
        // GUIDED CONVERSATION — priority order (most-specific first)
        // ═══════════════════════════════════════════════════════════

        // ── Stage 5: POST-SIMULATION → after table rendered ──
        // Detect simulation result in bot response (table with cicilan data)
        $hasSimResult = (str_contains($b, '| 1 |') || str_contains($b, '|1|') ||
                        str_contains($b, 'total cicilan') || str_contains($b, 'total bunga') ||
                        (str_contains($b, 'bulan') && str_contains($b, 'rp') && str_contains($b, 'sisa')));
        if ($hasSimResult) {
            return [
                '📊 Coba Metode Flat',
                '📈 Coba Metode Anuitas',
                '⚠️ Cek Info Denda',
                '✅ Ajukan Penyesuaian Sekarang',
            ];
        }

        // ── Stage 4: TENOR SELECTION → user already picked method ──
        $choseFlat    = str_contains($all, 'metode flat') || str_contains($all, 'pilih flat') || str_contains($all, 'gunakan flat');
        $choseAnuitas = str_contains($all, 'metode anuitas') || str_contains($all, 'pilih anuitas') || str_contains($all, 'gunakan anuitas');
        if ($choseFlat && !str_contains($u, 'bulan')) {
            return ['📊 Flat 3 Bulan', '📊 Flat 6 Bulan', '📊 Flat 12 Bulan', '✏️ Masukkan Tenor Lain'];
        }
        if ($choseAnuitas && !str_contains($u, 'bulan')) {
            return ['📈 Anuitas 3 Bulan', '📈 Anuitas 6 Bulan', '📈 Anuitas 12 Bulan', '✏️ Masukkan Tenor Lain'];
        }

        // ── Stage 3: METHOD SELECTION → amount provided, pick method ──
        $hasAmount = preg_match('/\d+\s*(juta|ribu|rp|rupiah|\.000)/i', $userMessage) ||
                     preg_match('/rp\s?[\d\.]+/i', $userMessage);
        $wantsSimulasi = str_contains($all, 'simulasi') || str_contains($all, 'hitung cicilan') ||
                         str_contains($all, 'kalkulasi') || str_contains($all, 'berapa cicilan');
        if ($wantsSimulasi && $hasAmount) {
            return [
                '📊 Pilih Metode Flat',
                '📈 Pilih Metode Anuitas',
                '⚖️ Bandingkan Flat vs Anuitas',
            ];
        }

        // ── Stage 2: AMOUNT ENTRY → user wants simulation, no amount yet ──
        if ($wantsSimulasi) {
            return [
                '💵 Simulasi Rp 50 Juta',
                '💵 Simulasi Rp 100 Juta',
                '💵 Simulasi Rp 250 Juta',
                '✏️ Masukkan Jumlah Manual',
            ];
        }

        // ── Stage 1a: PINJAMAN INFO → show loan menu ──
        if (str_contains($all, 'pinjam') || str_contains($all, 'invoice financing') ||
            str_contains($all, 'po financing') || str_contains($all, 'factoring') ||
            str_contains($all, 'pencairan') || str_contains($all, 'tenor 30')) {
            return [
                '🧮 Simulasi Cicilan Pinjaman',
                '⚠️ Info Denda & Jatuh Tempo',
                '📋 Syarat & Dokumen Pinjaman',
                '🔄 Ajukan Penyesuaian Cicilan',
            ];
        }

        // ── Stage 1b: DENDA / JATUH TEMPO ──
        if (str_contains($all, 'denda') || str_contains($all, 'jatuh tempo') ||
            str_contains($all, 'telat') || str_contains($all, 'macet') || str_contains($all, 'gagal bayar')) {
            return [
                '🔄 Ajukan Penyesuaian Cicilan',
                '🧮 Simulasi Cicilan Baru',
                '📅 Lihat Jadwal Cicilan',
                '📞 Hubungi Admin SYFA',
            ];
        }

        // ── Stage 1c: INVESTASI INFO ──
        if (str_contains($all, 'investasi') || str_contains($all, 'investasi reguler') ||
            str_contains($all, 'investasi khusus')) {
            return [
                '📦 Info Investasi Reguler',
                '⭐ Info Investasi Khusus',
                '⚖️ Bandingkan Reguler vs Khusus',
                '📝 Cara Daftar Investasi',
            ];
        }

        // ── Stage 1d: DOKUMEN / SYARAT / PROSES ──
        if (str_contains($all, 'dokumen') || str_contains($all, 'syarat') ||
            str_contains($all, 'cara ajukan') || str_contains($all, 'proses pengajuan')) {
            return [
                '📋 Dokumen Pinjaman',
                '📋 Dokumen Penyesuaian',
                '🧮 Simulasi Dulu',
                '📞 Hubungi Admin SYFA',
            ];
        }

        // ── Stage 1e: STATUS ──
        if (str_contains($all, 'status') || str_contains($all, 'pengajuan saya') ||
            str_contains($all, 'rekap') || str_contains($all, 'portofolio')) {
            return [
                '💼 Status Pinjaman',
                '📈 Status Investasi',
                '🔄 Status Penyesuaian',
                '📊 Rekap Portfolio',
            ];
        }

        // ── Default — initial / unknown ──
        return [
            '🧮 Simulasi Cicilan',
            '📅 Cek Status Pinjaman',
            '📈 Info Investasi',
            '⚠️ Info Denda & Jatuh Tempo',
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
