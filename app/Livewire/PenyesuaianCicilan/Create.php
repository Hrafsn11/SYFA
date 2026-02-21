<?php

namespace App\Livewire\ProgramRestrukturisasi;

use App\Helpers\ListNotifSFinance;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PengajuanRestrukturisasi;
use App\Models\ProgramRestrukturisasi;
use App\Models\JadwalAngsuran;
use App\Http\Requests\ProgramRestrukturisasiRequest;
use App\Livewire\Traits\HandleComponentEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class Create extends Component
{
    use WithFileUploads;
    use HandleComponentEvent;

    public bool $isEdit = false;
    public string $pageTitle = 'Form Tambah Jenis Program Restrukturisasi';
    public string $pageSubtitle = 'Buat program restrukturisasi berdasarkan pengajuan yang telah disetujui';
    public string $submitLabel = 'Simpan Program Restrukturisasi';

    // Form fields
    public $id_pengajuan_restrukturisasi = '';
    public $restrukturisasi;
    public $nama_debitur = '';
    public $nomor_kontrak = '';
    public $metode_perhitungan = 'Flat';
    public $plafon_pembiayaan = 0;
    public $suku_bunga_per_tahun = 12;
    public $jangka_waktu_total = 12;
    public $masa_tenggang = 2;
    public $tanggal_mulai_cicilan;
    public $nominal_yg_disetujui;

    // Calculated data
    public $jadwal_angsuran = [];
    public $total_pokok = 0;
    public $total_margin = 0;
    public $total_cicilan = 0;
    public $show_jadwal = false;
    public $specialCase = true;

    // Options
    public $approvedRestrukturisasi = [];

    public function mount(?string $id = null): void
    {
        $this->tanggal_mulai_cicilan = $this->tanggal_mulai_cicilan ?? date('Y-m-d');
        $this->show_jadwal = false;
        $this->specialCase = true;
        $this->jadwal_angsuran = [];
        $this->loadApprovedRestrukturisasi($id);
    }

    protected function approvalStatuses(): array
    {
        return ['Selesai', 'Disetujui CEO SKI', 'Disetujui Direktur SKI'];
    }

    protected function loadApprovedRestrukturisasi(?string $forceIncludeId = null): void
    {
        $user = Auth::user();
        $isAdmin = $user && $user->hasRole(['super-admin', 'admin', 'sfinance', 'Finance SKI']);

        $query = PengajuanRestrukturisasi::with('debitur')
            ->where(function ($q) {
                $q->whereIn('status', $this->approvalStatuses());
            })
            ->whereNotNull('sisa_pokok_belum_dibayar')
            ->where('sisa_pokok_belum_dibayar', '>', 0)
            // Exclude pengajuan yang sudah memiliki program restrukturisasi
            ->whereDoesntHave('programRestrukturisasi');

        if (!$isAdmin) {
            $debitur = \App\Models\MasterDebiturDanInvestor::where('user_id', Auth::id())->first();

            if ($debitur) {
                $query->where('id_debitur', $debitur->id_debitur);
            } else {
                $this->approvedRestrukturisasi = collect([]);
                return;
            }
        }

        if ($forceIncludeId) {
            $query->orWhere('id_pengajuan_restrukturisasi', $forceIncludeId);
        }

        $this->approvedRestrukturisasi = $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Override handleComponentEvent to trigger additional logic after Select2 changes
     * This is called when Select2 component dispatches 'select2-updated' event
     */
    #[\Livewire\Attributes\On('select2-updated')]
    #[\Livewire\Attributes\On('datepicker-updated')]
    #[\Livewire\Attributes\On('currency-updated')]
    public function handleComponentEvent($value, $modelName)
    {
        if (property_exists($this, $modelName)) {
            $this->{$modelName} = $value;

            // Trigger additional logic based on which field changed
            if ($modelName === 'id_pengajuan_restrukturisasi') {
                $this->loadPengajuanData();
            }
        }
    }

    public function updatedIdPengajuanRestrukturisasi()
    {
        $this->loadPengajuanData();
    }

    /**
     * Load data pengajuan restrukturisasi
     * Called when pengajuan selection changes
     */
    public function loadPengajuanData()
    {
        if ($this->id_pengajuan_restrukturisasi) {
            $this->restrukturisasi = PengajuanRestrukturisasi::with('debitur')
                ->find($this->id_pengajuan_restrukturisasi);

            if ($this->restrukturisasi) {
                $this->specialCase = true;
                if (in_array('Pengurangan tunggakan pokok/margin', $this->restrukturisasi->jenis_restrukturisasi)) {
                    $this->specialCase = false;
                }

                $this->nama_debitur = $this->restrukturisasi->debitur
                    ? $this->restrukturisasi->debitur->nama
                    : $this->restrukturisasi->nama_perusahaan;
                $this->nomor_kontrak = $this->restrukturisasi->nomor_kontrak_pembiayaan;
                // Plafon = Sisa Pokok + Tunggakan Margin (jika ada)
                $sisaPokok = (float) $this->restrukturisasi->sisa_pokok_belum_dibayar;
                $tunggakanMargin = (float) ($this->restrukturisasi->tunggakan_margin_bunga ?? 0);
                $this->plafon_pembiayaan = $sisaPokok + $tunggakanMargin;
            }
        } else {
            $this->nama_debitur = '';
            $this->nomor_kontrak = '';
            $this->plafon_pembiayaan = 0;
        }

        $this->metode_perhitungan = 'Flat';
        $this->suku_bunga_per_tahun = 12;
        $this->jangka_waktu_total = 12;
        $this->masa_tenggang = 2;
        $this->tanggal_mulai_cicilan = now()->format('Y-m-d');

        $this->show_jadwal = false;
        $this->jadwal_angsuran = [];
    }


    public function updated($propertyName)
    {
        $calculationFields = [
            'plafon_pembiayaan',
            'suku_bunga_per_tahun',
            'jangka_waktu_total',
            'masa_tenggang',
            'tanggal_mulai_cicilan',
            'metode_perhitungan'
        ];

        if (in_array($propertyName, $calculationFields)) {
            $this->show_jadwal = false;
            $this->jadwal_angsuran = [];
            $this->total_pokok = 0;
            $this->total_margin = 0;
            $this->total_cicilan = 0;
        }

        if ($propertyName === 'id_pengajuan_restrukturisasi') {
            $this->updatedIdPengajuanRestrukturisasi();
        }
    }

    /**
     * Hitung jadwal angsuran berdasarkan metode perhitungan
     * 
     * @return void
     */
    public function hitungJadwalAngsuran()
    {
        // VALIDATION GUARD: Pengajuan harus dipilih terlebih dahulu
        if (empty($this->id_pengajuan_restrukturisasi)) {
            $this->addError('id_pengajuan_restrukturisasi', 'Silakan pilih Pengajuan Restrukturisasi terlebih dahulu.');
            return;
        }

        // Sanitasi Input
        $this->plafon_pembiayaan = (float) $this->plafon_pembiayaan;
        $this->suku_bunga_per_tahun = (float) $this->suku_bunga_per_tahun;
        $this->jangka_waktu_total = (int) $this->jangka_waktu_total;
        $this->masa_tenggang = (int) $this->masa_tenggang;

        // Validate calculation parameters
        try {
            $this->validateCalculationParameters();
        } catch (ValidationException $e) {
            // Validation errors already added by validate()
            return;
        }

        // Validasi Logika Masa Tenggang
        if ($this->masa_tenggang >= $this->jangka_waktu_total) {
            $this->addError('masa_tenggang', 'Masa tenggang tidak boleh melebihi atau sama dengan jangka waktu total!');
            $this->show_jadwal = false;
            return;
        }

        // [LOGIC PERHITUNGAN DISINI]
        if (in_array('Pengurangan tunggakan pokok/margin', $this->restrukturisasi->jenis_restrukturisasi)) {
            $this->calculatePenguranganTunggakanPokok();
        } elseif ($this->metode_perhitungan === 'Flat') {
            $this->calculateFlatMethod();
        } elseif ($this->metode_perhitungan === 'Anuitas') {
            $this->calculateEfektifAnuitasMethod();
        }

        $this->show_jadwal = true;

        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Berhasil',
            'text' => 'Jadwal angsuran berhasil dihitung! Silakan simpan.',
        ]);
    }

    // [UPDATE] Metode Flat dengan Sisa Pinjaman
    // [KEMBALI KE VERSI LAMA - BERSIH]
    private function calculateFlatMethod()
    {
        $marginPerBulan = ($this->plafon_pembiayaan * ($this->suku_bunga_per_tahun / 100)) / 12;
        $bulanEfektifAnuitas = $this->jangka_waktu_total - $this->masa_tenggang;
        $pokokPerBulan = $bulanEfektifAnuitas > 0 ? $this->plafon_pembiayaan / $bulanEfektifAnuitas : 0;

        $jadwal = [];
        $totalPokok = 0;
        $totalMargin = 0;
        $totalCicilan = 0;
        $startDate = \Carbon\Carbon::parse($this->tanggal_mulai_cicilan);

        for ($i = 1; $i <= $this->jangka_waktu_total; $i++) {
            $dueDate = $startDate->copy()->addMonths($i - 1);
            $pokok = 0;
            $margin = $marginPerBulan;
            $catatan = '';

            if ($i <= $this->masa_tenggang) {
                $pokok = 0;
                $catatan = 'Masa Tenggang - Hanya Bayar Margin';
            } else {
                $pokok = $pokokPerBulan;
            }

            $total = $pokok + $margin;
            $totalPokok += $pokok;
            $totalMargin += $margin;
            $totalCicilan += $total;

            $jadwal[] = [
                'no' => $i,
                'tanggal_jatuh_tempo' => $dueDate->format('d F Y'),
                'tanggal_jatuh_tempo_raw' => $dueDate->format('Y-m-d'),
                // TIDAK ADA KEY 'sisa_pinjaman' DISINI
                'pokok' => $pokok,
                'margin' => $margin,
                'total_cicilan' => $total,
                'catatan' => $catatan,
                'is_grace_period' => $i <= $this->masa_tenggang,
            ];
        }

        $this->jadwal_angsuran = $jadwal;
        $this->total_pokok = $totalPokok;
        $this->total_margin = $totalMargin;
        $this->total_cicilan = $totalCicilan;
    }

    // [METODE Efektif (Anuitas) - ADA SISA PINJAMAN]
    private function calculateEfektifAnuitasMethod()
    {
        $bungaPerBulan = ($this->suku_bunga_per_tahun / 100) / 12;
        $sisaPokok = $this->plafon_pembiayaan;
        $bulanEfektifAnuitas = $this->jangka_waktu_total - $this->masa_tenggang;

        $cicilanEfektifAnuitasTetap = 0;
        if ($bulanEfektifAnuitas > 0 && $bungaPerBulan > 0) {
            $penyebut = 1 - pow(1 + $bungaPerBulan, -$bulanEfektifAnuitas);
            $cicilanEfektifAnuitasTetap = $sisaPokok * ($bungaPerBulan / $penyebut);
        }

        $jadwal = [];
        $totalPokok = 0;
        $totalMargin = 0;
        $totalCicilan = 0;
        $startDate = \Carbon\Carbon::parse($this->tanggal_mulai_cicilan);

        for ($i = 1; $i <= $this->jangka_waktu_total; $i++) {
            $dueDate = $startDate->copy()->addMonths($i - 1);
            $isGracePeriod = $i <= $this->masa_tenggang;

            // Simpan Sisa Pinjaman Awal Bulan untuk Ditampilkan
            $sisaPinjamanDisplay = $sisaPokok;

            $pokok = 0;
            $margin = 0;
            $total = 0;
            $catatan = '';

            if ($isGracePeriod) {
                $pokok = 0;
                $margin = $sisaPokok * $bungaPerBulan;
                $total = $margin;
                $catatan = 'Masa Tenggang - Hanya Bayar Margin';
            } else {
                $margin = $sisaPokok * $bungaPerBulan;

                if ($i == $this->jangka_waktu_total) {
                    $pokok = $sisaPokok;
                    $total = $pokok + $margin;
                } else {
                    $total = $cicilanEfektifAnuitasTetap;
                    $pokok = $total - $margin;
                }

                $sisaPokok -= $pokok;
                if ($sisaPokok < 0)
                    $sisaPokok = 0;
            }

            $totalPokok += $pokok;
            $totalMargin += $margin;
            $totalCicilan += $total;

            $jadwal[] = [
                'no' => $i,
                'tanggal_jatuh_tempo' => $dueDate->format('d F Y'),
                'tanggal_jatuh_tempo_raw' => $dueDate->format('Y-m-d'),
                'sisa_pinjaman' => $sisaPinjamanDisplay, // <--- HANYA ADA DI Efektif (Anuitas)
                'pokok' => $pokok,
                'margin' => $margin,
                'total_cicilan' => $total,
                'catatan' => $catatan,
                'is_grace_period' => $isGracePeriod,
            ];
        }

        $this->jadwal_angsuran = $jadwal;
        $this->total_pokok = $totalPokok;
        $this->total_margin = $totalMargin;
        $this->total_cicilan = $totalCicilan;
    }

    // Khususon Pengurangan tunggakan pokok/margin
    private function calculatePenguranganTunggakanPokok()
    {
        $totalPokok = $this->nominal_yg_disetujui;
        $bulan = $this->jangka_waktu_total;

        $cicilan = $totalPokok / $bulan;

        $jadwal = [];
        $totalMargin = 0;
        $totalCicilan = 0;

        for ($i = 1; $i <= $this->jangka_waktu_total; $i++) {
            $totalMargin += 0;
            $totalCicilan += $cicilan;

            $jadwal[] = [
                'no' => $i,
                'pokok' => $cicilan,
                'margin' => 0,
            ];
        }

        $this->jadwal_angsuran = $jadwal;
        $this->total_pokok = $totalPokok;
        $this->total_margin = $totalMargin;
        $this->total_cicilan = $totalCicilan;
    }

    /**
     * Simpan program restrukturisasi beserta jadwal angsuran
     * 
     * @return void
     */
    public function simpan()
    {
        // VALIDATION GUARD: Jadwal harus sudah dihitung
        if (empty($this->jadwal_angsuran) || !$this->show_jadwal) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Belum Dihitung',
                'text' => 'Silakan klik tombol "Hitung Jadwal Angsuran" terlebih dahulu.',
            ]);
            return;
        }

        // Validate using FormRequest rules directly
        try {
            $request = new ProgramRestrukturisasiRequest();

            $khususPenguranganTunggakanPokok = in_array('Pengurangan tunggakan pokok/margin', $this->restrukturisasi->jenis_restrukturisasi);

            $rules = $request->rules();
            if ($khususPenguranganTunggakanPokok) {
                $rules = [
                    "id_pengajuan_restrukturisasi" => "required|exists:pengajuan_restrukturisasi,id_pengajuan_restrukturisasi",
                    "plafon_pembiayaan" => "required|numeric|min:0",
                    "jangka_waktu_total" => "required|integer|min:1|max:360",
                    "nominal_yg_disetujui" => "required|integer|min:1",
                    "jadwal_angsuran" => "required|array|min:1",
                    "jadwal_angsuran.*.no" => "required|integer|min:1",
                    "jadwal_angsuran.*.pokok" => "required|numeric|min:0",
                    "jadwal_angsuran.*.margin" => "required|numeric|min:0",
                    "jadwal_angsuran.*.status" => "nullable|in:Belum Jatuh Tempo,Jatuh Tempo,Lunas",
                    "jadwal_angsuran.*.bukti_pembayaran" => "nullable|string",
                    "jadwal_angsuran.*.tanggal_bayar" => "nullable|date",
                    "jadwal_angsuran.*.nominal_bayar" => "nullable|numeric|min:0",
                    "total_pokok" => "required|numeric|min:0",
                    "total_margin" => "required|numeric|min:0",
                    "total_cicilan" => "required|numeric|min:0",
                ];
            }

            $this->validate(
                $rules,
                $request->messages(),
                $request->attributes()
            );

            // Additional business logic validation (since passedValidation doesn't work with Livewire)
            if (!$khususPenguranganTunggakanPokok && ($this->masa_tenggang >= $this->jangka_waktu_total)) {
                $this->addError('masa_tenggang', 'Masa tenggang tidak boleh lebih dari atau sama dengan jangka waktu total.');
                throw ValidationException::withMessages([
                    'masa_tenggang' => 'Masa tenggang tidak boleh lebih dari atau sama dengan jangka waktu total.',
                ]);
            }

            // Verify totals match
            $sumPokok = array_sum(array_column($this->jadwal_angsuran, 'pokok'));
            $sumMargin = array_sum(array_column($this->jadwal_angsuran, 'margin'));
            $tolerance = 1;

            if (abs($sumPokok - $this->total_pokok) > $tolerance) {
                throw ValidationException::withMessages([
                    'total_pokok' => 'Total pokok tidak sesuai dengan jumlah angsuran. Silakan hitung ulang.',
                ]);
            }

            if (abs($sumMargin - $this->total_margin) > $tolerance) {
                throw ValidationException::withMessages([
                    'total_margin' => 'Total margin tidak sesuai dengan jumlah angsuran. Silakan hitung ulang.',
                ]);
            }
        } catch (ValidationException $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Gagal Menyimpan',
                'text' => implode("\n", collect($e->errors())->flatten()->toArray()),
            ]);
            return; // Don't throw, just return
        }

        try {
            DB::beginTransaction();

            // Sanitasi metode_perhitungan
            $metodeValid = null;
            if (!$khususPenguranganTunggakanPokok) {
                $metodeValid = trim($this->metode_perhitungan);
                if (!in_array($metodeValid, ['Flat', 'Anuitas'])) {
                    throw new \Exception('Metode perhitungan tidak valid: ' . $metodeValid);
                }

                $filled = [
                    'id_pengajuan_restrukturisasi' => $this->id_pengajuan_restrukturisasi,
                    'metode_perhitungan' => $metodeValid,
                    'plafon_pembiayaan' => (float) $this->plafon_pembiayaan,
                    'suku_bunga_per_tahun' => (float) $this->suku_bunga_per_tahun,
                    'jangka_waktu_total' => (int) $this->jangka_waktu_total,
                    'masa_tenggang' => (int) $this->masa_tenggang,
                    'tanggal_mulai_cicilan' => $this->tanggal_mulai_cicilan,
                    'total_pokok' => (float) $this->total_pokok,
                    'total_margin' => (float) $this->total_margin,
                    'total_cicilan' => (float) $this->total_cicilan,
                    'status' => 'Menunggu Generate Kontrak',
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ];

            } else {
                $filled = [
                    'id_pengajuan_restrukturisasi' => $this->id_pengajuan_restrukturisasi,
                    'metode_perhitungan' => $metodeValid,
                    'plafon_pembiayaan' => (float) $this->plafon_pembiayaan,
                    'jangka_waktu_total' => (int) $this->jangka_waktu_total,
                    'nominal_yg_disetujui' => (double) $this->nominal_yg_disetujui,
                    'total_pokok' => (float) $this->total_pokok,
                    'total_margin' => (float) $this->total_margin,
                    'total_cicilan' => (float) $this->total_cicilan,
                    'status' => 'Menunggu Generate Kontrak',
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ];
            }

            $program = ProgramRestrukturisasi::create($filled);

            if (!in_array('Pengurangan tunggakan pokok/margin', $program->pengajuanRestrukturisasi->jenis_restrukturisasi)) {
                foreach ($this->jadwal_angsuran as $item) {
                    JadwalAngsuran::create([
                        'id_program_restrukturisasi' => $program->id_program_restrukturisasi,
                        'no' => $item['no'],
                        'tanggal_jatuh_tempo' => \Carbon\Carbon::parse($item['tanggal_jatuh_tempo_raw']),
                        'pokok' => $item['pokok'],
                        'margin' => $item['margin'],
                        'total_cicilan' => $item['total_cicilan'],
                        'catatan' => $item['catatan'],
                        'is_grace_period' => $item['is_grace_period'],
                        'status' => 'Belum Jatuh Tempo',
                    ]);
                }
            } else {
                foreach ($this->jadwal_angsuran as $item) {
                    JadwalAngsuran::create([
                        'id_program_restrukturisasi' => $program->id_program_restrukturisasi,
                        'no' => $item['no'],
                        'pokok' => $item['pokok'],
                        'margin' => 0,
                        'total_cicilan' => $item['pokok'],
                        'status' => 'Belum Jatuh Tempo',
                    ]);
                }
            }

            ListNotifSFinance::createProgramRestrukturisasi($program);
            DB::commit();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => 'Program restrukturisasi berhasil disimpan! Silakan lanjutkan untuk generate kontrak.',
                'redirect_url' => route('program-restrukturisasi.generate-kontrak', $program->id_program_restrukturisasi)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving: ' . $e->getMessage());
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'System Error',
                'text' => 'Gagal menyimpan: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Validate calculation parameters
     * 
     * @return void
     * @throws ValidationException
     */
    protected function validateCalculationParameters(): void
    {
        if (!in_array('Pengurangan tunggakan pokok/margin', $this->restrukturisasi->jenis_restrukturisasi)) {
            $this->validate([
                'plafon_pembiayaan' => 'required|numeric|min:1',
                'suku_bunga_per_tahun' => 'required|numeric|min:0|max:100',
                'jangka_waktu_total' => 'required|integer|min:1|max:360',
                'masa_tenggang' => 'required|integer|min:0|max:36',
                'tanggal_mulai_cicilan' => 'required|date|after_or_equal:today',
            ], [
                'plafon_pembiayaan.required' => 'Plafon pembiayaan wajib diisi.',
                'plafon_pembiayaan.min' => 'Plafon pembiayaan harus lebih dari 0.',
                'suku_bunga_per_tahun.max' => 'Suku bunga tidak boleh lebih dari 100%.',
                'jangka_waktu_total.min' => 'Jangka waktu minimal 1 bulan.',
                'jangka_waktu_total.max' => 'Jangka waktu maksimal 360 bulan (30 tahun).',
                'masa_tenggang.max' => 'Masa tenggang maksimal 36 bulan (3 tahun).',
                'tanggal_mulai_cicilan.after_or_equal' => 'Tanggal mulai cicilan tidak boleh kurang dari hari ini.',
            ]);

            // Additional business logic validation
            if ($this->masa_tenggang >= $this->jangka_waktu_total) {
                throw ValidationException::withMessages([
                    'masa_tenggang' => 'Masa tenggang tidak boleh lebih dari atau sama dengan jangka waktu total.',
                ]);
            }
        } else {
            $this->validate([
                'jangka_waktu_total' => 'required|integer|min:1|max:360',
                'nominal_yg_disetujui' => 'required|integer|min:1|max:' . $this->plafon_pembiayaan,
            ], [
                'jangka_waktu_total.required' => 'Jangka waktu harus diisi.',
                'jangka_waktu_total.min' => 'Jangka waktu minimal 1 bulan.',
                'jangka_waktu_total.max' => 'Jangka waktu maksimal 360 bulan (30 tahun).',
                'nominal_yg_disetujui.required' => 'Nominal yang Disetujui harus diisi.',
                'nominal_yg_disetujui.integer' => 'Nominal harus angka.',
                'nominal_yg_disetujui.min' => 'Nominal tidak boleh 0',
                'nominal_yg_disetujui.max' => 'Nominal tidak boleh lebih dari plafon',
            ]);
        }
    }

    /**
     * Check if program can be calculated
     * 
     * @return bool
     */
    public function getCanCalculateProperty(): bool
    {
        if ($this->restrukturisasi) {
            if (!in_array('Pengurangan tunggakan pokok/margin', $this->restrukturisasi->jenis_restrukturisasi)) {
                return !empty($this->id_pengajuan_restrukturisasi)
                    && $this->plafon_pembiayaan > 0
                    && $this->suku_bunga_per_tahun >= 0
                    && $this->jangka_waktu_total > 0
                    && !empty($this->tanggal_mulai_cicilan);
            } else {

                return !empty($this->id_pengajuan_restrukturisasi)
                    && $this->plafon_pembiayaan > 0
                    && $this->jangka_waktu_total > 0
                    && $this->nominal_yg_disetujui > 0;
            }
        }

        return false;
    }

    /**
     * Check if program can be saved
     * 
     * @return bool
     */
    public function getCanSaveProperty(): bool
    {
        return !empty($this->jadwal_angsuran)
            && $this->show_jadwal
            && !empty($this->id_pengajuan_restrukturisasi);
    }

    public function render()
    {
        return view('livewire.program-restrukturisasi.create')->layout('layouts.app');
    }
}
