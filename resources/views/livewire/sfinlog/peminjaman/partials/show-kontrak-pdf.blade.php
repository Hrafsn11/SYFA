<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kontrak Pembiayaan - {{ $data['nomor_kontrak'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            /* Font size standar surat */
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .mb-1 {
            margin-bottom: 4px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-3 {
            margin-bottom: 12px;
        }

        .mb-4 {
            margin-bottom: 24px;
        }

        .mb-5 {
            margin-bottom: 30px;
        }

        .mt-4 {
            margin-top: 24px;
        }

        .mt-5 {
            margin-top: 48px;
        }

        /* Table Layout for Content */
        table.legal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }

        table.legal-table td {
            vertical-align: top;
            padding-bottom: 10px;
        }

        .pl-3 {
            padding-left: 15px;
        }

        /* Signatures */
        table.signature-table {
            width: 100%;
            margin-top: 30px;
        }

        table.signature-table td {
            vertical-align: top;
            text-align: center;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Utilities */
        .w-100 {
            width: 100%;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <table width="100%">
        <tr>
            <td align="center">
                <!-- Gunakan public_path untuk image di DomPDF kalau local file, atau asset/base64 -->
                <!-- Untuk amannya menggunakan public_path jika server lokal -->
                <img src="{{ public_path('assets/img/branding/Logo.jpg') }}" alt="Logo"
                    style="height: 60px; margin-bottom: 10px;">
                <h3 class="fw-bold text-uppercase mb-1" style="font-size: 16pt; margin: 0;">Financing Contract</h3>
                <p class="fw-bold" style="margin: 0;">No: {{ $data['nomor_kontrak'] }}</p>
            </td>
        </tr>
    </table>

    <div class="mb-4"></div>

    @include('livewire.sfinlog.peminjaman.partials.show-kontrak-content')

    <!-- Tanda Tangan (Menggunakan Table agar rapi di PDF) -->
    <table class="signature-table">
        <tr>
            <td width="50%">
                <p><strong>Kreditur</strong></p>
                <p style="margin-bottom: 0;">CEO S-Finlog</p>

                <div style="height: 100px; position: relative;">
                    <!-- Use public_path for images in PDF -->
                    <img src="{{ public_path('assets/img/image.png') }}"
                        style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 150px; height: 80px; object-fit: contain; z-index: 1;">
                    <img src="{{ public_path('assets/img/TTD-CEO-FINLOG.png') }}"
                        style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%); width: 140px; height: 75px; object-fit: contain; z-index: 2;">
                </div>

                <p><strong>Rafi Ghani Razak</strong></p>
            </td>
            <td width="50%">
                <p><strong>Principal</strong></p>
                <p>{{ $data['nama_principal'] }}</p>

                <div style="height: 100px; display: flex; align-items: center; justify-content: center;">
                    @if ($peminjaman->debitur && $peminjaman->debitur->tanda_tangan)
                        <img src="{{ public_path('storage/' . $peminjaman->debitur->tanda_tangan) }}"
                            style="max-width: 150px; max-height: 80px;">
                    @else
                        <div style="height: 80px;"></div>
                    @endif
                </div>

                <p><strong>{{ $data['nama_pic'] }}</strong></p>
            </td>
        </tr>
    </table>

</body>

</html>
