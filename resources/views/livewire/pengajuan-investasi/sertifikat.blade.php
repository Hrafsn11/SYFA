<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Jenis Investasi - {{ $data['nomor_jenis investasi'] }}</title>
    <style>
        @font-face {
            font-family: 'Amsterdam Two';
            src: url('{{ url('assets/fonts/Amsterdam-Two-Font/amsterdam-two-ttf.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Quattrocento';
            src: url('{{ url('assets/fonts/Quattrocento/Quattrocento-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Quattrocento';
            src: url('{{ url('assets/fonts/Quattrocento/Quattrocento-Bold.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        @font-face {
            font-family: 'Poppins';
            src: url('{{ url('assets/fonts/Poppins/Poppins-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Poppins';
            src: url('{{ url('assets/fonts/Poppins/Poppins-Medium.ttf') }}') format('truetype');
            font-weight: 500;
            font-style: normal;
        }

        @font-face {
            font-family: 'Poppins';
            src: url('{{ url('assets/fonts/Poppins/Poppins-SemiBold.ttf') }}') format('truetype');
            font-weight: 600;
            font-style: normal;
        }

        @font-face {
            font-family: 'Poppins';
            src: url('{{ url('assets/fonts/Poppins/Poppins-Bold.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .page {
            width: 297mm;
            height: 210mm;
            background: #f5ede4;
            position: relative;
            margin: 0 auto 20px;
            page-break-after: always;
            background-image: 
                linear-gradient(to bottom, rgba(245, 237, 228, 0.8), rgba(235, 227, 218, 0.8));
        }

        .page:last-child {
            margin-bottom: 0;
        }

        /* Border ornament */
        .border-frame {
            position: absolute;
            top: 15mm;
            left: 15mm;
            right: 15mm;
            bottom: 15mm;
            border: 3px solid #8b6914;
            border-radius: 15px;
        }

        .border-frame::before {
            content: '';
            position: absolute;
            top: 5mm;
            left: 5mm;
            right: 5mm;
            bottom: 5mm;
            border: 1px solid #8b6914;
            border-radius: 10px;
        }

        /* Corner decorations */
        .corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 2px solid #8b6914;
        }

        .corner-tl {
            top: 12mm;
            left: 12mm;
            border-right: none;
            border-bottom: none;
            border-radius: 15px 0 0 0;
        }

        .corner-tr {
            top: 12mm;
            right: 12mm;
            border-left: none;
            border-bottom: none;
            border-radius: 0 15px 0 0;
        }

        .corner-bl {
            bottom: 12mm;
            left: 12mm;
            border-right: none;
            border-top: none;
            border-radius: 0 0 0 15px;
        }

        .corner-br {
            bottom: 12mm;
            right: 12mm;
            border-left: none;
            border-top: none;
            border-radius: 0 0 15px 0;
        }

        /* Logo */
        .logo {
            position: absolute;
            top: 22mm;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .logo-img {
            height: 60px;
            width: auto;
            display: block;
            margin: 0 auto;
        }

        /* Title */
        .title {
            position: absolute;
            top: 52mm;
            left: 50%;
            transform: translateX(-50%);
            font-size: 38px;
            font-family: 'Quattrocento', serif;
            color: #8b6914;
            font-weight: 700;
            letter-spacing: 3px;
            text-align: center;
        }

        /* Content */
        .content {
            position: absolute;
            top: 82mm;
            left: 45mm;
            right: 45mm;
        }

        .content-row {
            display: flex;
            margin-bottom: 7px;
            font-size: 13px;
            align-items: baseline;
        }

        .content-label {
            width: 200px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .content-separator {
            width: 25px;
            text-align: center;
            font-weight: 600;
        }

        .content-value {
            flex: 1;
            font-weight: 500;
            border-bottom: 1.5px solid #333;
            padding-bottom: 3px;
            padding-left: 5px;
        }

        /* Signature */
        .signature-area {
            position: absolute;
            bottom: 30mm;
            right: 55mm;
            text-align: center;
            width: 240px;
        }

        .signature-img {
            width: 150px;
            height: auto;
            display: block;
            margin: 0 auto 8px;
        }

        .signature-line {
            width: 100%;
            height: 2px;
            background: #333;
            margin: 8px 0;
        }

        .signature-name {
            font-size: 19px;
            font-weight: bold;
            margin-top: 8px;
            letter-spacing: 0.5px;
        }

        .signature-title {
            font-size: 14px;
            color: #555;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Badge/Seal - Hidden */
        .seal {
            display: none;
        }

        .seal-star {
            display: none;
        }

        /* Page 2 styles */
        .page2-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 75%;
        }

        .page2-intro {
            font-size: 20px;
            color: #555;
            margin-bottom: 35px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .page2-name {
            font-size: 48px;
            font-family: 'Amsterdam Two', cursive;
            color: #8b6914;
            margin: 25px 0;
            line-height: 1.3;
            font-weight: normal;
        }

        .page2-message {
            font-size: 17px;
            line-height: 2;
            color: #333;
            margin-top: 35px;
            font-weight: 400;
        }

        .page2-signature {
            position: absolute;
            bottom: 35mm;
            right: 55mm;
            text-align: center;
            width: 240px;
        }

        .page2-signature .seal {
            display: none;
        }

        .page2-signature .signature-img {
            width: 150px;
            height: auto;
            display: block;
            margin: 10px auto 8px;
        }

        .page2-signature .signature-line {
            width: 100%;
            height: 2px;
            background: #333;
            margin: 8px 0;
        }

        .page2-signature .signature-name {
            font-size: 19px;
            font-weight: bold;
            margin-top: 8px;
            letter-spacing: 0.5px;
        }

        .page2-signature .signature-title {
            font-size: 14px;
            color: #555;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Print styles */
        @media print {
            body {
                background: none;
            }
            
            .page {
                margin: 0;
                box-shadow: none;
            }
            
            @page {
                margin: 0;
            }
        }

        /* Corner decorative pattern - Page 2 */
        .corner-pattern {
            position: absolute;
            width: 100px;
            height: 100px;
        }

        .corner-pattern-tl {
            top: 20mm;
            left: 20mm;
            border-top: 3px solid #8b6914;
            border-left: 3px solid #8b6914;
        }

        .corner-pattern-tl::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            width: 40px;
            height: 40px;
            border-top: 2px solid #8b6914;
            border-left: 2px solid #8b6914;
        }

        .corner-pattern-tr {
            top: 20mm;
            right: 20mm;
            border-top: 3px solid #8b6914;
            border-right: 3px solid #8b6914;
        }

        .corner-pattern-tr::before {
            content: '';
            position: absolute;
            top: -10px;
            right: -10px;
            width: 40px;
            height: 40px;
            border-top: 2px solid #8b6914;
            border-right: 2px solid #8b6914;
        }

        .corner-pattern-bl {
            bottom: 20mm;
            left: 20mm;
            border-bottom: 3px solid #8b6914;
            border-left: 3px solid #8b6914;
        }

        .corner-pattern-bl::before {
            content: '';
            position: absolute;
            bottom: -10px;
            left: -10px;
            width: 40px;
            height: 40px;
            border-bottom: 2px solid #8b6914;
            border-left: 2px solid #8b6914;
        }

        .corner-pattern-br {
            bottom: 20mm;
            right: 20mm;
            border-bottom: 3px solid #8b6914;
            border-right: 3px solid #8b6914;
        }

        .corner-pattern-br::before {
            content: '';
            position: absolute;
            bottom: -10px;
            right: -10px;
            width: 40px;
            height: 40px;
            border-bottom: 2px solid #8b6914;
            border-right: 2px solid #8b6914;
        }

        /* Logo styling for page 2 */
        .page2-logo {
            position: absolute;
            top: 25mm;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .page2-logo .logo-img {
            height: 75px;
            width: auto;
        }

        /* Horizontal line decoration */
        .hr-decoration {
            position: absolute;
            height: 2px;
            background: linear-gradient(to right, transparent, #8b6914, transparent);
        }

        .hr-top {
            top: 55mm;
            left: 30%;
            right: 30%;
        }

        .hr-bottom {
            bottom: 40mm;
            left: 40%;
            right: 40%;
        }

        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .btn-download {
            background: #008080;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        .btn-download:hover {
            background: #006666;
        }

        @media print {
            .no-print {
                display: none;
            }

            /* Force print background colors and images */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            body {
                margin: 0;
                padding: 0;
            }
            
            .page {
                margin: 0;
                box-shadow: none;
                background: #f5ede4 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .border-frame,
            .corner,
            .corner-pattern {
                border-color: #8b6914 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .title,
            .page2-name {
                color: #8b6914 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .logo-text,
            .page2-logo-text {
                color: #008080 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                margin: 0;
                size: A4 landscape;
            }
        }
    </style>
</head>
<body>
    <!-- Download Button -->
    <div class="no-print">
        <button onclick="window.print()" class="btn-download">
            <i class="fa fa-download"></i> Download PDF
        </button>
    </div>

    <!-- Page 1: Certificate -->
    <div class="page">
        <!-- Corner decorations -->
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        <!-- Border frame -->
        <div class="border-frame"></div>

        <!-- Logo -->
        <div class="logo">
            <img src="{{ url('assets/img/image.png') }}" alt="Synnovac Capital" class="logo-img">
        </div>

        <!-- Title -->
        <div class="title">Certificate of Jenis Investasi</div>

        <!-- Content -->
        <div class="content">
            <div class="content-row">
                <div class="content-label">NAMA DEPOSAN</div>
                <div class="content-separator">:</div>
                <div class="content-value">{{ $data['nama_deposan'] }}</div>
            </div>
            <div class="content-row">
                <div class="content-label">NOMOR DEPOSITO</div>
                <div class="content-separator">:</div>
                <div class="content-value">{{ $data['nomor_jenis investasi'] }}</div>
            </div>
            <div class="content-row">
                <div class="content-label">DESKRIPSI</div>
                <div class="content-separator">:</div>
                <div class="content-value">{{ $data['deskripsi'] }}</div>
            </div>
            <div class="content-row">
                <div class="content-label">NILAI DEPOSITO</div>
                <div class="content-separator">:</div>
                <div class="content-value">{{ $data['nilai_jenis investasi'] }}</div>
            </div>
            <div class="content-row">
                <div class="content-label">KODE TRANSAKSI</div>
                <div class="content-separator">:</div>
                <div class="content-value">{{ $data['kode_transaksi'] }}</div>
            </div>
            <div class="content-row">
                <div class="content-label">JANGKA WAKTU</div>
                <div class="content-separator">:</div>
                <div class="content-value">{{ $data['jangka_waktu'] }}</div>
            </div>
            <div class="content-row">
                <div class="content-label">BAGI HASIL</div>
                <div class="content-separator">:</div>
                <div class="content-value">{{ $data['bagi_hasil'] }}</div>
            </div>
        </div>

        <!-- Seal/Badge -->
        <div class="seal">
            <div class="seal-star">★</div>
        </div>

        <!-- Signature -->
        <div class="signature-area">
            <img src="{{ url('assets/img/ttd-ceo-ski.png') }}" alt="Signature" class="signature-img" onerror="this.style.display='none'">
            <div class="signature-line"></div>
            <div class="signature-name">M. Kurniawan</div>
            <div class="signature-title">CEO PT. SKI</div>
        </div>
    </div>

    <!-- Page 2: Thank You Page -->
    <div class="page">
        <!-- Corner patterns -->
        <div class="corner-pattern corner-pattern-tl"></div>
        <div class="corner-pattern corner-pattern-tr"></div>
        <div class="corner-pattern corner-pattern-bl"></div>
        <div class="corner-pattern corner-pattern-br"></div>

        <!-- Logo -->
        <div class="page2-logo">
            <img src="{{ url('assets/img/scapitals.png') }}" alt="Synnovac Capital" class="logo-img">
        </div>

        <!-- Decorative lines -->
        <div class="hr-decoration hr-top"></div>

        <!-- Content -->
        <div class="page2-content">
            <div class="page2-intro">dengan bangga memberikan penghargaan kepada:</div>
            
            <div class="page2-name">{{ $data['nama_deposan'] }}</div>
            
            <div class="page2-message">
                telah menjadi investor S-Finance dengan nilai {{ $data['nilai_investasi_text'] }}<br>
                semoga membawa manfaat dan keberkahan
            </div>
        </div>

        <!-- Decorative line bottom -->
        <div class="hr-decoration hr-bottom"></div>

        <!-- Signature -->
        <div class="page2-signature">
            <div class="seal">
                <div class="seal-star">★</div>
            </div>
            <img src="{{ url('assets/img/ttd-ceo-ski.png') }}" alt="Signature" class="signature-img" onerror="this.style.display='none'">
            <div class="signature-line"></div>
            <div class="signature-name">M. Kurniawan</div>
            <div class="signature-title">CEO PT. SKI</div>
        </div>
    </div>

    <script>
        // Auto print on load
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
