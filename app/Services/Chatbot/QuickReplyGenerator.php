<?php

namespace App\Services\Chatbot;

final class QuickReplyGenerator
{
    /**
     * @return array<int, string>
     */
    public function generate(string $userMessage, string $botResponse): array
    {
        $u = strtolower($userMessage);
        $b = strtolower($botResponse);
        $all = $u . ' ' . $b;

        $hasSimResult = (str_contains($b, '| 1 |') || str_contains($b, '|1|') ||
            str_contains($b, 'total cicilan') || str_contains($b, 'total bunga') ||
            (str_contains($b, 'bulan') && str_contains($b, 'rp') && str_contains($b, 'sisa')));
        if ($hasSimResult) {
            return [
                '📊 Coba Metode Flat',
                '📈 Coba Metode Anuitas',
                '⚠️ Cek Info Denda',
                '✅ Ajukan Penyesuaian Sekarang',
            ];
        }

        $choseFlat = str_contains($all, 'metode flat') || str_contains($all, 'pilih flat') || str_contains($all, 'gunakan flat');
        $choseAnuitas = str_contains($all, 'metode anuitas') || str_contains($all, 'pilih anuitas') || str_contains($all, 'gunakan anuitas');
        if ($choseFlat && !str_contains($u, 'bulan')) {
            return ['📊 Flat 3 Bulan', '📊 Flat 6 Bulan', '📊 Flat 12 Bulan', '✏️ Masukkan Tenor Lain'];
        }
        if ($choseAnuitas && !str_contains($u, 'bulan')) {
            return ['📈 Anuitas 3 Bulan', '📈 Anuitas 6 Bulan', '📈 Anuitas 12 Bulan', '✏️ Masukkan Tenor Lain'];
        }

        $hasAmount = preg_match('/\d+\s*(juta|ribu|rp|rupiah|\.000)/i', $userMessage) ||
            preg_match('/rp\s?[\d\.]+/i', $userMessage);
        $wantsSimulasi = str_contains($all, 'simulasi') || str_contains($all, 'hitung cicilan') ||
            str_contains($all, 'kalkulasi') || str_contains($all, 'berapa cicilan');
        if ($wantsSimulasi && $hasAmount) {
            return [
                '📊 Pilih Metode Flat',
                '📈 Pilih Metode Anuitas',
                '⚖️ Bandingkan Flat vs Anuitas',
            ];
        }

        if ($wantsSimulasi) {
            return [
                '💵 Simulasi Rp 50 Juta',
                '💵 Simulasi Rp 100 Juta',
                '💵 Simulasi Rp 250 Juta',
                '✏️ Masukkan Jumlah Manual',
            ];
        }

        if (str_contains($all, 'pinjam') || str_contains($all, 'invoice financing') ||
            str_contains($all, 'po financing') || str_contains($all, 'factoring') ||
            str_contains($all, 'pencairan') || str_contains($all, 'tenor 30')) {
            return [
                '🧮 Simulasi Cicilan Pinjaman',
                '⚠️ Info Denda & Jatuh Tempo',
                '📋 Syarat & Dokumen Pinjaman',
                '🔄 Ajukan Penyesuaian Cicilan',
            ];
        }

        if (str_contains($all, 'denda') || str_contains($all, 'jatuh tempo') ||
            str_contains($all, 'telat') || str_contains($all, 'macet') || str_contains($all, 'gagal bayar')) {
            return [
                '🔄 Ajukan Penyesuaian Cicilan',
                '🧮 Simulasi Cicilan Baru',
                '📅 Lihat Jadwal Cicilan',
                '📞 Hubungi Admin SYFA',
            ];
        }

        if (str_contains($all, 'investasi') || str_contains($all, 'investasi reguler') ||
            str_contains($all, 'investasi khusus')) {
            return [
                '📦 Info Investasi Reguler',
                '⭐ Info Investasi Khusus',
                '⚖️ Bandingkan Reguler vs Khusus',
                '📝 Cara Daftar Investasi',
            ];
        }

        if (str_contains($all, 'dokumen') || str_contains($all, 'syarat') ||
            str_contains($all, 'cara ajukan') || str_contains($all, 'proses pengajuan')) {
            return [
                '📋 Dokumen Pinjaman',
                '📋 Dokumen Penyesuaian',
                '🧮 Simulasi Dulu',
                '📞 Hubungi Admin SYFA',
            ];
        }

        if (str_contains($all, 'status') || str_contains($all, 'pengajuan saya') ||
            str_contains($all, 'rekap') || str_contains($all, 'portofolio')) {
            return [
                '💼 Status Pinjaman',
                '📈 Status Investasi',
                '🔄 Status Penyesuaian',
                '📊 Rekap Portfolio',
            ];
        }

        return [
            '🧮 Simulasi Cicilan',
            '📅 Cek Status Pinjaman',
            '📈 Info Investasi',
            '⚠️ Info Denda & Jatuh Tempo',
        ];
    }
}
