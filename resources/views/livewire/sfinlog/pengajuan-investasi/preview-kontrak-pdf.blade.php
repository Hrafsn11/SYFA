<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kontrak Investasi - {{ $data['nomor_kontrak'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
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

        .text-end {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
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

        .mt-3 {
            margin-top: 16px;
        }

        .mt-5 {
            margin-top: 48px;
        }

        /* Table Signatures */
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
    </style>
</head>

<body>

    <!-- Header -->
    <table width="100%">
        <tr>
            <td align="right">
                <img src="{{ public_path('assets/img/branding/Logo.jpg') }}" alt="Logo"
                    style="height: 60px; margin-bottom: 10px;">
            </td>
        </tr>
        <tr>
            <td align="center">
                <h3 class="fw-bold mb-2" style="font-size: 14pt; margin: 0; text-transform: uppercase;">SURAT PERJANJIAN
                    KERJASAMA INVESTASI PEMBIAYAAN USAHA</h3>
                <p class="fw-bold" style="margin: 0;">No: {{ $data['nomor_kontrak'] }}</p>
            </td>
        </tr>
    </table>

    <div class="mb-5"></div>

    <!-- Content -->
    @include('livewire.sfinlog.pengajuan-investasi.partials.preview-kontrak-content')

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td width="50%">
                <p><strong>PIHAK PERTAMA</strong></p>

                <div style="height: 100px; display: flex; align-items: center; justify-content: center;">
                    @if ($pengajuan->investor && $pengajuan->investor->tanda_tangan)
                        <img src="{{ public_path('storage/' . $pengajuan->investor->tanda_tangan) }}"
                            style="max-width: 150px; max-height: 80px;">
                    @else
                        <div style="height: 80px;"></div>
                    @endif
                </div>

                <p><strong>{{ $data['nama_investor'] }}</strong></p>
                <p>{{ $data['nama_perusahaan'] }}</p>
            </td>
            <td width="50%">
                <p><strong>PIHAK KEDUA</strong></p>

                <div style="height: 100px; position: relative;">
                    <img src="{{ public_path('assets/img/image.png') }}"
                        style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 150px; height: 80px; object-fit: contain; z-index: 1;">
                    <img src="{{ public_path('assets/img/TTD-CEO-FINLOG.png') }}"
                        style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%); width: 140px; height: 75px; object-fit: contain; z-index: 2;">
                </div>

                <p><strong>Rafi Ghani Razak</strong></p>
                <p>CEO S-Finlog</p>
            </td>
        </tr>
    </table>

</body>

</html>
