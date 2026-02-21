<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Peringatan Tagihan Tagihan Pinjaman Pembiayaan</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #002147; padding: 20px; text-align: center;"></td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background-color:rgb(255, 255, 255); padding: 20px; text-align: center;">
                          <!-- <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/PELNI_2023.svg/999px-PELNI_2023.svg.png" alt="PELNI Logo" width="120"> -->
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px; font-family: Arial, sans-serif; color: #333;">
                            <h2 style="margin-top: 0; color: #002147;">Peringatan Tagihan Tagihan Pinjaman Pembiayaan</h2>
                            <p>Yth. Bapak/Ibu <strong>{{ $user }}</strong></p>
                            <p>Dengan hormat,</p>
                        </td>
                    </tr>
                    <tr>
                      <td style="background-color: #fff3cd; text-align: left; padding: 10px 20px;">
                          <div style="font-size: 14px; padding: 10px 10px;">
                              <p style="margin: 0;">{!! $content !!}</p>
                              <p style="margin: 0; color: #F2994A">Demikian kami sampaikan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.</p>
                          </div>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding: 30px; font-family: Arial, sans-serif; color: #333;">
                          <p style="text-align: center; margin: 30px 0;">
                              <a href="{{ $url }}" style="background-color: #001a57; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Masuk ke Aplikasi</a>
                          </p>

                          <p><strong>Hormat Kami,</strong><br>Financial Planner<br><br>PT Synovac Capital Indonesia</p>
                      </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #002147; color: #ffffff; font-size: 12px; text-align: left; padding: 30px 20px;">
                            <!-- <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/PELNI_2023.svg/999px-PELNI_2023.svg.png" alt="PELNI Logo" width="80" style="margin-bottom: 10px;"><br> -->
                            <!-- <p style="margin: 0; color: #ffffff;"><strong>Copyright © 2025</strong><br>
                                PT Pelayaran Nasional Indonesia (PELNI)</p><br>

                            <p style="margin: 0; color: #ffffff;"><strong>Alamat Kantor Pusat PELNI:</strong><br>
                                Jl. Gajah Mada No.14, RT.1/RW.5,<br>
                                Petojo Utara, Kecamatan Gambir,<br>
                                Jakarta Pusat 10130, Indonesia</p><br> -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
