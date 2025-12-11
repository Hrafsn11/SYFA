<?php

namespace App\Livewire\PengajuanPinjaman\FieldsInput;

use App\Attributes\FieldInput;
use App\Enums\JenisPembiayaanEnum;

trait FieldInputCreate
{
    #[FieldInput]
    public $sumber_pembiayaan = 'Eksternal', 
        $id_instansi, 
        $nama_rekening, 
        $lampiran_sid, 
        $tujuan_pembiayaan, 
        $jenis_pembiayaan = JenisPembiayaanEnum::INVOICE_FINANCING, 
        $harapan_tanggal_pencairan, 
        $rencana_tgl_pembayaran, 
        $tenor_pembayaran, 
        $catatan_lainnya, 
        $form_data_invoice = [];
}
