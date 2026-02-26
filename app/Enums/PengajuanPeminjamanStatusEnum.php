<?php

namespace App\Enums;

class PengajuanPeminjamanStatusEnum
{
    use EnumTrait;

    const DRAFT                      = 'Draft';
    const SUBMIT_DOKUMEN             = 'Submit Dokumen';
    const VALIDASI_DITOLAK           = 'Validasi Ditolak';
    const DOKUMEN_TERVALIDASI        = 'Dokumen Tervalidasi';
    const DEBITUR_SETUJU             = 'Debitur Setuju';
    const DISETUJUI_CEO              = 'Disetujui oleh CEO SKI';
    const DITOLAK_CEO                = 'Ditolak oleh CEO SKI';
    const DISETUJUI_DIREKTUR         = 'Disetujui oleh Direktur SKI';
    const DITOLAK_DIREKTUR           = 'Ditolak oleh Direktur SKI';
    const MENUNGGU_KONFIRMASI        = 'Menunggu Konfirmasi Debitur';
    const KONFIRMASI_DISETUJUI       = 'Konfirmasi Disetujui Debitur';
    const KONFIRMASI_DITOLAK         = 'Konfirmasi Ditolak Debitur';
}