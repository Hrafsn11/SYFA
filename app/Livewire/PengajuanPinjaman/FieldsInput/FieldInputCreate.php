<?php

namespace App\Livewire\PengajuanPinjaman\FieldsInput;

use App\Attributes\FieldInput;
use App\Enums\JenisPembiayaanEnum;

trait FieldInputCreate
{
    #[FieldInput]
    public $sumber_pembiayaan = 'Eksternal';
    public $id_instansi;
    public $nama_rekening;
    public $lampiran_sid;
    public $tujuan_pembiayaan;
    public $jenis_pembiayaan = JenisPembiayaanEnum::INVOICE_FINANCING;
    public $tanggal_pencairan;
    public $tanggal_pembayaran;
    public $tenor_pembayaran;
    public $catatan_lainnya;
    public $form_data_invoice = [];
}
