<?php

namespace App\Livewire;

use App\Models\ProgramRestrukturisasi;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProgramRestrukturisasiIndex extends DataTableComponent
{
    protected $model = ProgramRestrukturisasi::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_program_restrukturisasi')
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setSearchStatus(true)
            ->setColumnSelectStatus(true)
            ->setEmptyMessage('Tidak ada data program restrukturisasi');
    }

    public function builder(): Builder
    {
        return ProgramRestrukturisasi::query()
            ->with(['pengajuanRestrukturisasi.debitur', 'creator'])
            ->select('program_restrukturisasi.*');
    }

    public function columns(): array
    {
        return [
            Column::make('No')
                ->label(function () {
                    static $index = 0;
                    return ++$index;
                }),
            
            Column::make('Nama Debitur', 'pengajuanRestrukturisasi.debitur.nama')
                ->label(fn($row) => $row->pengajuanRestrukturisasi?->debitur?->nama ?? $row->pengajuanRestrukturisasi?->nama_perusahaan ?? '-')
                ->searchable()
                ->sortable(),
            
            Column::make('Nomor Kontrak', 'pengajuanRestrukturisasi.nomor_kontrak_pembiayaan')
                ->label(fn($row) => $row->pengajuanRestrukturisasi?->nomor_kontrak_pembiayaan ?? '-')
                ->searchable()
                ->sortable(),
            
            Column::make('Metode', 'metode_perhitungan')
                ->label(fn($row) => '<span class="badge bg-info">' . $row->metode_perhitungan . '</span>')
                ->html()
                ->sortable(),
            
            Column::make('Plafon Pembiayaan', 'plafon_pembiayaan')
                ->label(fn($row) => 'Rp ' . number_format($row->plafon_pembiayaan, 0, ',', '.'))
                ->sortable(),
            
            Column::make('Suku Bunga', 'suku_bunga_per_tahun')
                ->label(fn($row) => $row->suku_bunga_per_tahun . '%')
                ->sortable(),
            
            Column::make('Jangka Waktu', 'jangka_waktu_total')
                ->label(fn($row) => $row->jangka_waktu_total . ' bulan')
                ->sortable(),
            
            Column::make('Masa Tenggang', 'masa_tenggang')
                ->label(fn($row) => $row->masa_tenggang . ' bulan')
                ->sortable(),
            
            Column::make('Total Cicilan', 'total_cicilan')
                ->label(fn($row) => 'Rp ' . number_format($row->total_cicilan, 0, ',', '.'))
                ->sortable(),
            
            Column::make('Dibuat Oleh', 'creator.name')
                ->label(fn($row) => $row->creator?->name ?? '-')
                ->sortable(),
            
            Column::make('Tanggal Dibuat', 'created_at')
                ->label(fn($row) => $row->created_at->format('d/m/Y H:i'))
                ->sortable(),
            
            Column::make('Aksi')
                ->label(function ($row) {
                    $detailUrl = route('program-restrukturisasi.show', $row->id_program_restrukturisasi);
                    $editUrl   = route('program-restrukturisasi.edit', $row->id_program_restrukturisasi);
            
                    return view('components.table-actions', compact('detailUrl', 'editUrl'));
                })
                ->html(),
        ];
    }
}