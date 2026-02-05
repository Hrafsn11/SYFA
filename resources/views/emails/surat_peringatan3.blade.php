<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Peringatan Over Due</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.6;
        }

        .container {
            width: 100%;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .content {
            margin-top: 20px;
        }

        .signature {
            margin-top: 40px;
        }

        .signature-name {
            margin-top: 60px;
            font-weight: bold;
        }

        .cc {
            margin-top: 30px;
            font-size: 11pt;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="title">
        <div>Format Surat Peringatan - OVER DUE</div>
        <div>Surat Peringatan ke-3</div>
    </div>

    <div class="content">
        <p>
            Kepada yth,<br>
            <strong>FP. {{ $debitur }}</strong>
        </p>

        <p>
            Bersama ini kami perlu informasikan Kembali bahwa tagihan piutang pembiayaan atas invoice no <strong>{{ $invoice }}</strong> sudah jatuh tempo <strong>180 hari</strong> dan telah kami ingatkan untuk yang ke 3 kalinya melalui Surat Peringatan maupun secara verbal, namun hingga hari ini kami belum mendapatkan realisasi pembayaran piutang pembiayaan tersebut, untuk itu mohon kiranya dapat mengkonfirmasikan kepada Luthfia ke 0858 9246 7566 atau melalui email <strong><a href="mailto:luthfia@synnovac-capital.com">luthfia@synnovac-capital.com</a></strong> dan mengembalikan dana pembiayaan tersebut SEGERA kepada {{ $finance_finlog }} ke rekening nomor <strong>{{ $no_rek }} PT Synnovac Kapital Indonesia</strong>.
        </p>
        @php
            $textKol = '';
            if($kol == 0 || $kol == 1){
                $textKol = 'LANCAR';
            }else if($kol == 2){
                $textKol = 'DALAM PERHATIAN KHUSUS';
            }else if($kol == 3){
                $textKol = 'TIDAK LANCAR';
            }else if($kol == 4){
                $textKol = 'DIRAGUKAN';
            }else if($kol == 5){
                $textKol = 'MACET';
            }
        @endphp
        <p>
            Status kolektibilitas pembiayaan saat ini adalah :
            <br>
            <strong>
                {{ $textKol }}
            </strong>
            <br>
            <em>
                (Isi salah satu kondisi sesuai waktu keterlambatannya / aging)
            </em>
        </p>
        <p>
            Demikian kiranya untuk menjadi perhatian agar pembiayaan ini dapat berjalan
            dengan baik dan membawa manfaat serta keberkahan bagi usaha kita bersama.
        </p>

        <p>
            Terimakasih atas kerjasamanya yang sangat baik.
        </p>

        <p>
            Salam Sehat &amp; Barokah
        </p>
    </div>

    <div class="signature">
        <div class="signature-name">
            Luthfia
        </div>
    </div>

    <div class="cc">
        <strong>CC :</strong><br>
        Komisaris PT {{ $debitur }}<br>
    </div>

</div>
</body>
</html>