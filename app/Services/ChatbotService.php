<?php

namespace App\Services;

use App\Models\User;
use App\Models\PengajuanPeminjaman;
use App\Models\PengajuanCicilan;
use App\Models\PenyesuaianCicilan;
use App\Models\PengajuanInvestasi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChatbotService
{
    protected string $apiUrl;
    protected string $model;
    protected string $apiKey;
    protected bool   $isLocal;

    public function __construct()
    {
        $driver = config('services.llm.driver', 'nvidia');
        $this->isLocal = ($driver === 'local');

        match ($driver) {
            'groq' => [
                $this->apiUrl = 'https://api.groq.com/openai/v1/chat/completions',
                $this->model  = 'llama-3.1-8b-instant',
                $this->apiKey = config('services.groq.api_key', ''),
            ],
            'local' => [
                $base = rtrim(config('services.lmstudio.base_url', 'http://127.0.0.1:1234'), '/'),
                $this->apiUrl = $base . '/v1/chat/completions',
                $this->model  = config('services.lmstudio.model', 'google/gemma-3-4b'),
                $this->apiKey = '',
            ],
            'nvidia' => [
                $this->apiUrl = 'https://integrate.api.nvidia.com/v1/chat/completions',
                $this->model  = config('services.nvidia.model', 'moonshotai/kimi-k2.5'),
                $this->apiKey = config('services.nvidia.api_key', ''),
            ],
            default => [ // kimi
                $this->apiUrl = 'https://api.moonshot.ai/v1/chat/completions',
                $this->model  = config('services.kimi.model', 'kimi-k2-turbo-preview'),
                $this->apiKey = config('services.kimi.api_key', ''),
            ],
        };
    }

    /**
     * Kirim pesan ke Groq dengan konteks data SYFA user.
     */
    public function chat(User $user, string $message, array $history = []): array
    {
        $systemPrompt = $this->buildSystemPrompt($user, $message);
        $messages     = $this->buildMessages($systemPrompt, $history, $message);

        try {
            $headers = ['Content-Type' => 'application/json'];
            if (!$this->isLocal && $this->apiKey) {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }

            $driver = config('services.llm.driver', 'nvidia');

            $payload = [
                'model'       => $this->model,
                'messages'    => $messages,
                'temperature' => ($driver === 'nvidia') ? 0.15 : 0.4,
            ];

            // Local  : tanpa batas — model tentukan sendiri
            // Kimi   : 4096 (moonshot-v1-8k support 8k context)
            // Groq   : 768 hemat kuota RPD
            // NVIDIA : 2048, mistral-large-3
            if ($driver === 'groq') {
                $payload['max_tokens'] = 768;
            } elseif ($driver === 'kimi') {
                $payload['max_tokens'] = 4096;
            } elseif ($driver === 'nvidia') {
                $payload['max_tokens']         = 2048;
                $payload['top_p']              = 1.0;
                $payload['frequency_penalty']  = 0.0;
                $payload['presence_penalty']   = 0.0;
            }

            $timeout = ($this->isLocal || $driver === 'nvidia') ? 120 : 60;
            $response = Http::timeout($timeout)
                ->withHeaders($headers)
                ->post($this->apiUrl, $payload);

            if ($response->failed()) {
                $status = $response->status();
                $driver = config('services.llm.driver', 'nvidia');
                Log::error("LLM API error [{$driver}]", ['status' => $status, 'body' => $response->body()]);

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
        $driver  = config('services.llm.driver', 'nvidia');
        $maxResp = ($driver === 'groq')
            ? 'Maks respons: 4 paragraf atau 1 tabel.'
            : 'Jawab selengkap yang diperlukan; tabel boleh penuh semua baris.';

        $base = <<<PROMPT
Kamu adalah **SYFA Assistant**, konsultan keuangan digital internal SYFA (Captive Finance Grup Holding).
Hari ini: {$today} | Pengguna: **{$user->name}** ({$peran})

ATURAN: Bahasa Indonesia formal. Sebut "Finance Officer". Bold angka rupiah & status penting.
{$maxResp} Tolak topik di luar keuangan SYFA.
Tutup tiap jawaban dengan kalimat pemandu langkah berikutnya.

PRODUK SYFA:
- Pinjaman: Invoice/PO Financing/Factoring/Installment, tenor 30 hari. Dokumen: KTP Direksi, NPWP, Akta, Rek Koran 3 bln, Laporan Keuangan.
- Penyesuaian Cicilan: Flat (cicilan tetap) atau Anuitas (bunga menurun). Bunga & tenor ditetapkan Admin.
- Investasi: Reguler (bunga standar) atau Khusus (bunga tinggi).

ALUR SIMULASI — ikuti ketat:
S1: Topik pinjaman → tawarkan: Simulasi / Info Denda / Syarat / Cara Ajukan.
S2: Simulasi diminta → tanya jumlah pokok.
S3: Jumlah masuk → tanya metode Flat atau Anuitas (1 kalimat penjelasan tiap metode).
S4: Metode dipilih → tanya tenor. Jika user kirim "Flat 6 Bulan" → LANGSUNG hitung.
S5: Tampilkan tabel + tabel perbandingan Flat vs Anuitas → tawarkan ajukan/coba tenor lain.
S-DENDA: Jika tanya denda/jatuh tempo → jelaskan konsekuensi → tawarkan Penyesuaian.
PROMPT;

        // ── 2. DYNAMIC DATA (hanya fetch + inject berdasarkan topik pesan) ─────
        $dataBlock = $isAdmin
            ? "\n[MODE ADMIN — tidak ada data pinjaman/investasi pribadi. Bantu pertanyaan umum SYFA.]"
            : $this->buildDynamicContext($debitur, $message);

        // ── 3. RUMUS (inject hanya saat simulasi diperlukan, ~80 token) ────────
        $formulaBlock = '';
        if (preg_match('/simulasi|hitung|cicilan|flat|anuitas|tenor/i', $message)) {
            $formulaBlock = "\nRUMUS: FLAT: cicilan=(P+P×r×n)/n, r=rate%/12."
                . " ANUITAS: r=rate%/1200; cicilan=P×r×(1+r)^n/((1+r)^n-1)."
                . " Tabel: |No|Pokok|Bunga|Cicilan|Sisa| min 3 baris+Total."
                . " Jika rate tidak ada, pakai estimasi 2%/bln *(aktual ditentukan Admin SYFA)*.";
        }

        return $base . $dataBlock . $formulaBlock;
    }

    /**
     * Fetch & format data keuangan user berdasarkan kata kunci pesan.
     * Hanya query yang relevan yang dijalankan.
     */
    protected function buildDynamicContext($debitur, string $message): string
    {
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
            $pinjaman = PengajuanPeminjaman::where('id_debitur', $debitur->id_debitur)
                ->whereNotIn('status', ['draft', 'rejected', 'cancelled', 'completed', 'paid'])
                ->latest()
                ->get(['no_kontrak', 'jenis_pembiayaan', 'total_pinjaman', 'status',
                       'tanggal_jatuh_tempo', 'sisa_bayar_pokok']);

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
            $pending = PengajuanPeminjaman::where('id_debitur', $debitur->id_debitur)
                ->whereIn('status', ['draft', 'pending', 'review', 'waiting', 'verifikasi'])
                ->latest()
                ->get(['jenis_pembiayaan', 'total_pinjaman', 'status']);

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
            $investasi = PengajuanInvestasi::where('id_debitur_dan_investor', $debitur->id_debitur)
                ->whereNotIn('status', ['draft', 'rejected', 'cancelled'])
                ->latest()
                ->get(['nomor_kontrak', 'jenis_investasi', 'jumlah_investasi', 'bunga_pertahun', 'status']);

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
        $pinjamanCount  = PengajuanPeminjaman::where('id_debitur', $debitur->id_debitur)
            ->whereNotIn('status', ['draft', 'rejected', 'cancelled', 'completed', 'paid'])
            ->count();
        $investasiCount = PengajuanInvestasi::where('id_debitur_dan_investor', $debitur->id_debitur)
            ->whereNotIn('status', ['draft', 'rejected', 'cancelled'])
            ->count();

        $ctx = "\n## Ringkasan Akun\n- Pinjaman aktif: {$pinjamanCount}\n- Investasi aktif: {$investasiCount}\n";

        // Cek alert urgent (hanya select kolom minimal)
        $pinjaman = PengajuanPeminjaman::where('id_debitur', $debitur->id_debitur)
            ->whereNotIn('status', ['draft', 'rejected', 'cancelled', 'completed', 'paid'])
            ->get(['no_kontrak', 'tanggal_jatuh_tempo', 'sisa_bayar_pokok']);

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
     * Bangun array messages untuk Groq / OpenAI-compatible API (multi-turn conversation).
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
    protected function generateQuickReplies(string $userMessage, string $botResponse): array
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
