<?php

namespace App\Console\Commands;

use App\Models\HistoryStatusPengajuanInvestor;
use App\Models\MasterDebiturDanInvestor;
use App\Models\PengajuanInvestasi;
use App\Models\PengembalianInvestasi;
use App\Models\PenyaluranDanaInvestasi;
use App\Models\RiwayatPengembalianDanaInvestasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedDummyInvestasiData extends Command
{
    protected $signature = 'app:seed-dummy-investasi
        {--count=24 : Jumlah data pengajuan investasi yang akan dibuat}
        {--year= : Tahun data dummy (default: tahun berjalan)}
        {--reset : Hapus data dummy lama sebelum generate ulang}
        {--clean-only : Hanya hapus data dummy tanpa generate data baru}';

    protected $description = 'Generate data dummy investasi SFinance yang realistis untuk kebutuhan demo/screenshot';

    private const CONTRACT_PREFIX = 'DUMINV';
    private const DUMMY_DOMAIN = 'dummy-investasi.local';

    public function handle(): int
    {
        $count = (int) $this->option('count');
        $year = (int) ($this->option('year') ?: now()->year);
        $shouldReset = (bool) $this->option('reset');
        $cleanOnly = (bool) $this->option('clean-only');

        if ($cleanOnly) {
            DB::beginTransaction();
            try {
                $this->cleanupExistingDummyData();
                DB::commit();

                $this->info('Data dummy investasi berhasil dihapus. Seeder tetap tersedia untuk dipakai lagi.');
                $this->comment('Untuk generate ulang: php artisan app:seed-dummy-investasi --count=24');

                return self::SUCCESS;
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->error('Gagal menghapus data dummy: ' . $th->getMessage());
                return self::FAILURE;
            }
        }

        if ($count < 1) {
            $this->error('Nilai --count minimal 1');
            return self::FAILURE;
        }

        if ($year < 2020 || $year > 2100) {
            $this->error('Nilai --year tidak valid');
            return self::FAILURE;
        }

        $this->warn('Seeder dummy investasi akan membuat data untuk tampilan profesional (demo/screenshot).');

        DB::beginTransaction();

        try {
            $actorUser = $this->resolveActorUser();

            if ($shouldReset) {
                $this->cleanupExistingDummyData();
            }

            $investors = $this->prepareInvestorMasterData();
            $debitors = $this->prepareDebitorMasterData();

            $createdPengajuan = 0;
            $createdPenyaluran = 0;
            $createdRiwayat = 0;
            $createdPengembalian = 0;

            for ($i = 1; $i <= $count; $i++) {
                $investor = $investors[array_rand($investors)];
                $tanggalInvestasi = $this->generateTanggalInvestasi($year, $i);
                $jumlahInvestasi = $this->randomAmount(150_000_000, 1_500_000_000);
                $lamaInvestasiOptions = [3, 6, 9, 12, 18];
                $bungaTahunanOptions = [7, 8, 9, 10, 11, 12, 13, 14];

                $lamaInvestasi = $lamaInvestasiOptions[array_rand($lamaInvestasiOptions)];
                $bungaTahunan = $bungaTahunanOptions[array_rand($bungaTahunanOptions)];

                $nominalBunga = round(($jumlahInvestasi * ($bungaTahunan / 100) * ($lamaInvestasi / 12)), 2);

                $statusProfile = $this->resolveStatusProfile($i);

                $pengajuan = PengajuanInvestasi::create([
                    'id_debitur_dan_investor' => $investor->id_debitur,
                    'nama_investor' => $this->generateNamaInvestor($investor->nama, $i),
                    'nama_pic_kontrak' => $this->generatePicName($i),
                    'jenis_investasi' => $i % 4 === 0 ? 'Khusus' : 'Reguler',
                    'tanggal_investasi' => $tanggalInvestasi,
                    'lama_investasi' => $lamaInvestasi,
                    'jumlah_investasi' => $jumlahInvestasi,
                    'bunga_pertahun' => $bungaTahunan,
                    'nominal_bunga_yang_didapatkan' => $nominalBunga,
                    'status' => $statusProfile['status'],
                    'current_step' => $statusProfile['step'],
                    'created_by' => $actorUser->id,
                    'updated_by' => $actorUser->id,
                    'nomor_kontrak' => $statusProfile['with_contract']
                        ? sprintf('%s/%d/%04d', self::CONTRACT_PREFIX, $year, $i)
                        : null,
                    'sisa_pokok' => $jumlahInvestasi,
                    'sisa_bunga' => $nominalBunga,
                    'total_disalurkan' => 0,
                    'total_kembali_dari_penyaluran' => 0,
                ]);

                $createdPengajuan++;

                $this->createStatusHistory($pengajuan, $statusProfile['status'], $actorUser->id, $tanggalInvestasi);

                if (!$statusProfile['with_contract']) {
                    continue;
                }

                $penyaluranData = $this->createPenyaluranData($pengajuan, $debitors, $actorUser->id);
                $createdPenyaluran += $penyaluranData['penyaluran_count'];
                $createdRiwayat += $penyaluranData['riwayat_count'];

                $pengembalianCount = $this->createPengembalianKeInvestor($pengajuan, $actorUser->id);
                $createdPengembalian += $pengembalianCount;

                $pengajuan->refresh();

                if ($pengajuan->sisa_pokok <= 0 && $pengajuan->sisa_bunga <= 0) {
                    $pengajuan->update([
                        'status' => 'Lunas',
                        'current_step' => 6,
                    ]);
                } elseif (!in_array($pengajuan->status, ['Lunas', 'Ditolak', 'Draft'], true)) {
                    $pengajuan->update([
                        'status' => 'Selesai',
                        'current_step' => max(5, (int) $pengajuan->current_step),
                    ]);
                }
            }

            DB::commit();

            $this->newLine();
            $this->info('Dummy data investasi berhasil dibuat.');
            $this->line('- Pengajuan investasi: ' . $createdPengajuan);
            $this->line('- Penyaluran dana investasi: ' . $createdPenyaluran);
            $this->line('- Riwayat pengembalian dana investasi: ' . $createdRiwayat);
            $this->line('- Pengembalian investasi ke investor: ' . $createdPengembalian);
            $this->newLine();
            $this->comment('Jalankan ulang dengan reset: php artisan app:seed-dummy-investasi --reset');

            return self::SUCCESS;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->error('Gagal generate dummy data: ' . $th->getMessage());
            return self::FAILURE;
        }
    }

    private function resolveActorUser(): User
    {
        $user = User::query()->first();

        if ($user) {
            return $user;
        }

        return User::create([
            'name' => 'Dummy Admin Investasi',
            'email' => 'dummy.investasi@' . self::DUMMY_DOMAIN,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }

    private function cleanupExistingDummyData(): void
    {
        $dummyPengajuanIds = PengajuanInvestasi::query()
            ->where('nomor_kontrak', 'like', self::CONTRACT_PREFIX . '/%')
            ->orWhereHas('investor', function ($query) {
                $query->where('email', 'like', '%@' . self::DUMMY_DOMAIN);
            })
            ->pluck('id_pengajuan_investasi')
            ->all();

        if (!empty($dummyPengajuanIds)) {
            $penyaluranIds = PenyaluranDanaInvestasi::query()
                ->whereIn('id_pengajuan_investasi', $dummyPengajuanIds)
                ->pluck('id_penyaluran_dana_investasi')
                ->all();

            if (!empty($penyaluranIds)) {
                RiwayatPengembalianDanaInvestasi::query()
                    ->whereIn('id_penyaluran_dana_investasi', $penyaluranIds)
                    ->delete();
            }

            PengembalianInvestasi::query()
                ->whereIn('id_pengajuan_investasi', $dummyPengajuanIds)
                ->delete();

            HistoryStatusPengajuanInvestor::query()
                ->whereIn('id_pengajuan_investasi', $dummyPengajuanIds)
                ->delete();

            PenyaluranDanaInvestasi::query()
                ->whereIn('id_pengajuan_investasi', $dummyPengajuanIds)
                ->delete();

            PengajuanInvestasi::query()
                ->whereIn('id_pengajuan_investasi', $dummyPengajuanIds)
                ->delete();
        }

        MasterDebiturDanInvestor::query()
            ->where('email', 'like', '%@' . self::DUMMY_DOMAIN)
            ->delete();
    }

    /**
     * @return array<int, MasterDebiturDanInvestor>
     */
    private function prepareInvestorMasterData(): array
    {
        $investorCompanies = [
            'PT Aruna Cipta Ventura',
            'PT Bintang Harmoni Investama',
            'PT Cakra Dana Nusantara',
            'PT Dharma Kapital Sejahtera',
            'PT Eka Prima Aset',
            'PT Fajar Mitra Korporasi',
            'PT Griya Sentosa Investindo',
            'PT Hastana Mega Investama',
        ];

        $result = [];
        foreach ($investorCompanies as $idx => $companyName) {
            $result[] = MasterDebiturDanInvestor::create([
                'nama' => $companyName,
                'kode_perusahaan' => 'DMYI' . str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT),
                'alamat' => 'Jl. Sudirman Kav. ' . (20 + $idx) . ', Jakarta',
                'email' => 'investor' . ($idx + 1) . '@' . self::DUMMY_DOMAIN,
                'no_telepon' => '0812' . str_pad((string) random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'status' => 'active',
                'nama_ceo' => 'CEO ' . Str::after($companyName, 'PT '),
                'nama_direktur_holding' => 'Direktur Holding ' . ($idx + 1),
                'nama_komisaris' => 'Komisaris ' . ($idx + 1),
                'nama_bank' => 'BCA',
                'no_rek' => '777' . str_pad((string) random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'flagging' => 'ya',
                'flagging_investor' => 'sfinance',
            ]);
        }

        return $result;
    }

    /**
     * @return array<int, MasterDebiturDanInvestor>
     */
    private function prepareDebitorMasterData(): array
    {
        $debitorCompanies = [
            'PT Solusi Logistik Utama',
            'PT Maju Teknika Indonesia',
            'PT Karya Distribusi Nasional',
            'PT Prima Retail Nusantara',
            'PT Sinar Energi Mandiri',
            'PT Metro Supply Chain',
        ];

        $result = [];
        foreach ($debitorCompanies as $idx => $companyName) {
            $result[] = MasterDebiturDanInvestor::create([
                'nama' => $companyName,
                'kode_perusahaan' => 'DMYD' . str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT),
                'alamat' => 'Kawasan Industri Blok ' . chr(65 + $idx) . ', Bekasi',
                'email' => 'debitur' . ($idx + 1) . '@' . self::DUMMY_DOMAIN,
                'no_telepon' => '0821' . str_pad((string) random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'status' => 'active',
                'nama_ceo' => 'Direktur Utama ' . ($idx + 1),
                'nama_bank' => 'Mandiri',
                'no_rek' => '888' . str_pad((string) random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'flagging' => 'tidak',
            ]);
        }

        return $result;
    }

    /**
     * @return array{status:string,step:int,with_contract:bool}
     */
    private function resolveStatusProfile(int $index): array
    {
        $profiles = [
            ['status' => 'Draft', 'step' => 1, 'with_contract' => false],
            ['status' => 'Submitted', 'step' => 1, 'with_contract' => false],
            ['status' => 'Ditolak', 'step' => 2, 'with_contract' => false],
            ['status' => 'Generate Kontrak', 'step' => 5, 'with_contract' => true],
            ['status' => 'Selesai', 'step' => 6, 'with_contract' => true],
            ['status' => 'Lunas', 'step' => 6, 'with_contract' => true],
            ['status' => 'Disetujui oleh CEO SKI', 'step' => 4, 'with_contract' => true],
        ];

        return $profiles[$index % count($profiles)];
    }

    private function generateTanggalInvestasi(int $year, int $index): Carbon
    {
        $currentYear = (int) now()->year;
        $maxMonth = $year === $currentYear ? (int) now()->month : 12;
        $maxMonth = max(1, $maxMonth);

        if ($index <= 6) {
            $month = max(1, $maxMonth - ($index % 2));
        } else {
            $month = random_int(1, $maxMonth);
        }

        $day = random_int(2, 24);

        return Carbon::create($year, $month, $day);
    }

    private function generateNamaInvestor(string $companyName, int $index): string
    {
        $firstNames = [
            'Andi',
            'Bimo',
            'Cahya',
            'Dian',
            'Eka',
            'Fajar',
            'Galih',
            'Hendra',
            'Indra',
            'Joko',
            'Kevin',
            'Lukman',
        ];

        $lastNames = [
            'Pratama',
            'Saputra',
            'Nugroho',
            'Wijaya',
            'Santoso',
            'Kusuma',
            'Mahendra',
            'Setiawan',
            'Permana',
            'Ramadhan',
        ];

        $firstName = $firstNames[$index % count($firstNames)];
        $lastName = $lastNames[$index % count($lastNames)];

        return $firstName . ' ' . $lastName . ' (' . Str::after($companyName, 'PT ') . ')';
    }

    private function generatePicName(int $index): string
    {
        $picPool = [
            'Andi Pratama',
            'Budi Santoso',
            'Citra Maharani',
            'Dimas Nugroho',
            'Erika Wulandari',
            'Fajar Hidayat',
        ];

        return $picPool[$index % count($picPool)];
    }

    private function createStatusHistory(PengajuanInvestasi $pengajuan, string $currentStatus, string $actorId, Carbon $baseDate): void
    {
        $trail = [
            ['status' => 'Draft', 'step' => 1],
        ];

        if (in_array($currentStatus, ['Submitted', 'Disetujui oleh CEO SKI', 'Generate Kontrak', 'Selesai', 'Lunas'], true)) {
            $trail[] = ['status' => 'Submit Dokumen', 'step' => 2];
            $trail[] = ['status' => 'Dokumen Tervalidasi', 'step' => 3];
        }

        if (in_array($currentStatus, ['Disetujui oleh CEO SKI', 'Generate Kontrak', 'Selesai', 'Lunas'], true)) {
            $trail[] = ['status' => 'Disetujui oleh CEO SKI', 'step' => 4];
        }

        if (in_array($currentStatus, ['Generate Kontrak', 'Selesai', 'Lunas'], true)) {
            $trail[] = ['status' => 'Generate Kontrak', 'step' => 5];
        }

        if (in_array($currentStatus, ['Selesai', 'Lunas'], true)) {
            $trail[] = ['status' => 'Selesai', 'step' => 6];
        }

        if ($currentStatus === 'Ditolak') {
            $trail[] = ['status' => 'Ditolak', 'step' => 2];
        }

        if ($currentStatus === 'Submitted') {
            $trail[] = ['status' => 'Submitted', 'step' => 1];
        }

        if ($currentStatus === 'Lunas') {
            $trail[] = ['status' => 'Lunas', 'step' => 6];
        }

        $trail = collect($trail)
            ->unique(fn(array $item) => $item['status'])
            ->values();

        foreach ($trail as $idx => $item) {
            $historyDate = $baseDate->copy()->addDays($idx * 7);

            HistoryStatusPengajuanInvestor::create([
                'id_pengajuan_investasi' => $pengajuan->id_pengajuan_investasi,
                'submit_step1_by' => $actorId,
                'date' => $historyDate->toDateString(),
                'time' => $historyDate->copy()->setTime(9 + $idx, 15)->toTimeString(),
                'status' => $item['status'],
                'current_step' => $item['step'],
                'approve_by' => in_array($item['status'], ['Dokumen Tervalidasi', 'Disetujui oleh CEO SKI', 'Generate Kontrak', 'Selesai', 'Lunas'], true) ? $actorId : null,
                'reject_by' => $item['status'] === 'Ditolak' ? $actorId : null,
                'validasi_bagi_hasil' => $item['status'] === 'Ditolak' ? 'ditolak' : 'disetujui',
                'catatan' => $item['status'] === 'Ditolak'
                    ? 'Dokumen belum memenuhi ketentuan administrasi.'
                    : 'Progress verifikasi berjalan normal.',
                'catatan_validasi_dokumen_ditolak' => $item['status'] === 'Ditolak'
                    ? 'Silakan lengkapi dokumen legalitas dan mutasi rekening 3 bulan terakhir.'
                    : null,
            ]);
        }
    }

    /**
     * @param array<int, MasterDebiturDanInvestor> $debitors
     * @return array{penyaluran_count:int,riwayat_count:int}
     */
    private function createPenyaluranData(PengajuanInvestasi $pengajuan, array $debitors, string $actorId): array
    {
        $penyaluranCount = random_int(1, 3);
        $remainingForDistribution = (float) $pengajuan->jumlah_investasi * random_int(45, 85) / 100;

        $totalDisalurkan = 0.0;
        $totalKembali = 0.0;
        $createdPenyaluranCount = 0;
        $createdRiwayatCount = 0;

        for ($i = 1; $i <= $penyaluranCount; $i++) {
            if ($remainingForDistribution < 5_000_000) {
                break;
            }

            $isLast = $i === $penyaluranCount;

            if ($isLast) {
                $nominalDisalurkan = round($remainingForDistribution, 2);
            } else {
                $upper = max(8_000_000, (int) floor($remainingForDistribution / 2));
                $nominalDisalurkan = $this->randomAmount(8_000_000, $upper);
            }

            $remainingForDistribution -= $nominalDisalurkan;

            $tanggalKirim = Carbon::parse($pengajuan->tanggal_investasi)
                ->copy()
                ->addDays(random_int(2, 35));
            $tanggalTargetBalik = $tanggalKirim->copy()->addDays(random_int(30, 140));

            $penyaluran = PenyaluranDanaInvestasi::create([
                'id_pengajuan_investasi' => $pengajuan->id_pengajuan_investasi,
                'id_debitur' => $debitors[array_rand($debitors)]->id_debitur,
                'nominal_yang_disalurkan' => $nominalDisalurkan,
                'nominal_yang_dikembalikan' => 0,
                'tanggal_pengiriman_dana' => $tanggalKirim->toDateString(),
                'tanggal_pengembalian' => $tanggalTargetBalik->toDateString(),
            ]);

            $createdPenyaluranCount++;
            $totalDisalurkan += $nominalDisalurkan;

            $riwayatProfile = random_int(0, 2);
            $riwayatReturnTotal = 0.0;

            if ($riwayatProfile > 0) {
                $targetReturn = $riwayatProfile === 2
                    ? $nominalDisalurkan
                    : round($nominalDisalurkan * random_int(30, 75) / 100, 2);

                $riwayatItems = $riwayatProfile === 2 ? random_int(2, 3) : random_int(1, 2);
                $remainingReturn = $targetReturn;

                for ($x = 1; $x <= $riwayatItems; $x++) {
                    $isLastReturn = $x === $riwayatItems;
                    if ($isLastReturn) {
                        $nominalRiwayat = round($remainingReturn, 2);
                    } else {
                        $maxChunk = max(500_000, (int) floor($remainingReturn / 2));
                        $nominalRiwayat = $this->randomAmount(500_000, $maxChunk);
                    }

                    $remainingReturn -= $nominalRiwayat;

                    RiwayatPengembalianDanaInvestasi::create([
                        'id_penyaluran_dana_investasi' => $penyaluran->id_penyaluran_dana_investasi,
                        'nominal_dikembalikan' => $nominalRiwayat,
                        'tanggal_pengembalian' => $tanggalKirim->copy()->addDays(random_int(20, 120))->toDateString(),
                        'bukti_pengembalian' => 'dummy/bukti-pengembalian/riwayat-' . Str::lower(Str::random(8)) . '.pdf',
                        'catatan' => $riwayatProfile === 2
                            ? 'Pengembalian berjalan sesuai jadwal.'
                            : 'Pengembalian bertahap menyesuaikan arus kas.',
                        'diinput_oleh' => $actorId,
                    ]);

                    $createdRiwayatCount++;
                    $riwayatReturnTotal += $nominalRiwayat;
                }
            }

            $penyaluran->update([
                'nominal_yang_dikembalikan' => round($riwayatReturnTotal, 2),
            ]);

            $totalKembali += $riwayatReturnTotal;
        }

        $pengajuan->update([
            'total_disalurkan' => round($totalDisalurkan, 2),
            'total_kembali_dari_penyaluran' => round($totalKembali, 2),
        ]);

        return [
            'penyaluran_count' => $createdPenyaluranCount,
            'riwayat_count' => $createdRiwayatCount,
        ];
    }

    private function createPengembalianKeInvestor(PengajuanInvestasi $pengajuan, string $actorId): int
    {
        $pengajuan->refresh();

        $transactionCount = random_int(1, 3);
        $createdCount = 0;

        for ($i = 1; $i <= $transactionCount; $i++) {
            $pengajuan->refresh();

            $sisaPokok = (float) $pengajuan->sisa_pokok;
            $sisaBunga = (float) $pengajuan->sisa_bunga;

            if ($sisaPokok <= 0 && $sisaBunga <= 0) {
                break;
            }

            $sisaDanaDiPerusahaan = max(0, (float) $pengajuan->total_disalurkan - (float) $pengajuan->total_kembali_dari_penyaluran);
            $danaTersedia = max(0, $sisaPokok - $sisaDanaDiPerusahaan);

            if ($danaTersedia <= 0 && $sisaBunga <= 0) {
                break;
            }

            $pokokDibayar = 0.0;
            if ($danaTersedia > 0) {
                if ($i === $transactionCount) {
                    $pokokDibayar = $danaTersedia;
                } else {
                    $pokokDibayar = round($danaTersedia * random_int(20, 55) / 100, 2);
                }
            }

            $bungaDibayar = 0.0;
            if ($sisaBunga > 0) {
                if ($i === $transactionCount && $pokokDibayar >= $danaTersedia) {
                    $bungaDibayar = $sisaBunga;
                } else {
                    $bungaDibayar = round($sisaBunga * random_int(25, 65) / 100, 2);
                }
            }

            if ($pokokDibayar <= 0 && $bungaDibayar <= 0) {
                break;
            }

            $tanggalPengembalian = Carbon::parse($pengajuan->tanggal_investasi)
                ->copy()
                ->addDays(random_int(35, 220));
            if ($tanggalPengembalian->gt(now())) {
                $tanggalPengembalian = now()->copy()->subDays(random_int(1, 7));
            }

            PengembalianInvestasi::create([
                'id_pengajuan_investasi' => $pengajuan->id_pengajuan_investasi,
                'dana_pokok_dibayar' => round($pokokDibayar, 2),
                'bunga_dibayar' => round($bungaDibayar, 2),
                'bukti_transfer' => 'dummy/bukti-transfer/transfer-' . Str::lower(Str::random(8)) . '.pdf',
                'tanggal_pengembalian' => $tanggalPengembalian->toDateString(),
                'created_by' => $actorId,
            ]);

            $pengajuan->update([
                'sisa_pokok' => max(0, round($sisaPokok - $pokokDibayar, 2)),
                'sisa_bunga' => max(0, round($sisaBunga - $bungaDibayar, 2)),
            ]);

            $createdCount++;
        }

        return $createdCount;
    }

    private function randomAmount(int $min, int $max): float
    {
        if ($min >= $max) {
            return (float) $min;
        }

        $raw = random_int($min, $max);

        return (float) (round($raw / 1_000_000) * 1_000_000);
    }
}

//Hapus data dummy saja:
//php artisan app:seed-dummy-investasi --clean-only
//Kalau nanti mau isi lagi:
//php artisan app:seed-dummy-investasi --count=24
//Kalau mau hapus lalu isi ulang sekaligus:
//php artisan app:seed-dummy-investasi --reset --count=24