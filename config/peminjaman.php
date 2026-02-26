<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Director Approval Threshold
    |--------------------------------------------------------------------------
    |
    | Nominal disetujui (dalam Rupiah) yang menentukan apakah persetujuan
    | Direktur SKI diperlukan. Jika nominal_yang_disetujui >= nilai ini,
    | persetujuan Direktur wajib dilalui. Jika lebih kecil, step Direktur
    | dilewati otomatis setelah CEO menyetujui.
    |
    | Default: Rp 300.000.000 (300 juta)
    |
    */
    'director_approval_threshold' => env('PEMINJAMAN_DIRECTOR_THRESHOLD', 300_000_000),

];
