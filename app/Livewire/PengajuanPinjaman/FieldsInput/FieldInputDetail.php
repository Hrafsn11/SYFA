<?php

namespace App\Livewire\PengajuanPinjaman\FieldsInput;

use App\Attributes\FieldInput;

/**
 * Trait FieldInputDetail
 * 
 * Berisi properti-properti yang digunakan pada halaman detail peminjaman.
 * Properti dikelompokkan berdasarkan fungsinya.
 */
trait FieldInputDetail
{
    // ============================================
    // DATA PERUSAHAAN
    // ============================================

    #[FieldInput]
    public $nama_perusahaan;

    #[FieldInput]
    public $nama_ceo;

    #[FieldInput]
    public $alamat;

    #[FieldInput]
    public $nama_bank;

    #[FieldInput]
    public $no_rekening;

    #[FieldInput]
    public $nama_rekening;

    #[FieldInput]
    public $lampiran_sid;

    #[FieldInput]
    public $nilai_kol;

    #[FieldInput]
    public $tanda_tangan;

    // ============================================
    // DATA PEMINJAMAN UTAMA
    // ============================================

    #[FieldInput]
    public $nomor_peminjaman;

    #[FieldInput]
    public $no_kontrak;

    #[FieldInput]
    public $jenis_pembiayaan;

    #[FieldInput]
    public $sumber_pembiayaan;

    #[FieldInput]
    public $instansi;

    #[FieldInput]
    public $tujuan_pembiayaan;

    #[FieldInput]
    public $catatan_lainnya;

    #[FieldInput]
    public $status;

    // ============================================
    // DATA NOMINAL & TANGGAL
    // ============================================

    #[FieldInput]
    public $nominal_pinjaman;

    #[FieldInput]
    public $nominal_yang_disetujui;

    #[FieldInput]
    public $harapan_tanggal_pencairan;

    #[FieldInput]
    public $tanggal_pencairan;

    #[FieldInput]
    public $rencana_tgl_pembayaran;

    #[FieldInput]
    public $persentase_bagi_hasil;

    #[FieldInput]
    public $total_bagi_hasil;

    #[FieldInput]
    public $pembayaran_total;

    // ============================================
    // DATA KHUSUS INSTALLMENT
    // ============================================

    #[FieldInput]
    public $tenor_pembayaran;

    #[FieldInput]
    public $pps;

    #[FieldInput]
    public $s_finance;

    #[FieldInput]
    public $yang_harus_dibayarkan;

    // ============================================
    // DATA KHUSUS FACTORING
    // ============================================

    #[FieldInput]
    public $total_nominal_yang_dialihkan;

    // ============================================
    // UPLOAD & DOKUMEN
    // ============================================

    #[FieldInput]
    public $upload_bukti_transfer;

    public $dokumen_transfer; // Livewire file upload property

    // ============================================
    // WORKFLOW & STATE
    // ============================================

    public int $currentStep = 1;

    public int $totalSteps = 9;

    public $preview_no_kontrak;

    // ============================================
    // APPROVAL FORM FIELDS
    // ============================================

    public $deviasi;

    public $catatan_approval;

    public $biaya_administrasi;

    // ============================================
    // HISTORY & DATA COLLECTIONS
    // ============================================

    public $latestHistory;

    public $allHistory = [];

    public $detailsData = [];
}
