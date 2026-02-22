$replacements = @(
    @("HistoryStatusPengajuanRestrukturisasi", "HistoryStatusPengajuanCicilan"),
    @("EvaluasiAnalisaRestrukturisasi", "EvaluasiAnalisaCicilan"),
    @("EvaluasiPengajuanRestrukturisasi", "EvaluasiPengajuanCicilan"),
    @("PersetujuanKomiteRestrukturisasi", "PersetujuanKomiteCicilan"),
    @("RiwayatPembayaranRestrukturisasi", "RiwayatPembayaranCicilan"),
    @("ProgramRestrukturisasi", "PenyesuaianCicilan"),
    @("PengajuanRestrukturisasi", "PengajuanCicilan"),
    @("id_pengajuan_restrukturisasi", "id_pengajuan_cicilan"),
    @("id_program_restrukturisasi", "id_penyesuaian_cicilan"),
    @("id_evaluasi_restrukturisasi", "id_evaluasi_cicilan"),
    @("id_history_status_restrukturisasi", "id_history_status_cicilan"),
    @("id_analisa_restrukturisasi", "id_analisa_cicilan"),
    @("total_pengembalian_bagi_hasil", "total_pengembalian_bunga"),
    @("total_bagi_hasil_saat_ini", "total_bunga_saat_ini"),
    @("persentase_bagi_hasil", "persentase_bunga"),
    @("total_bagi_hasil", "total_bunga"),
    @("sisa_bagi_hasil", "sisa_bunga"),
    @("nilai_bagi_hasil", "nilai_bunga"),
    @("kurang_bayar_bagi_hasil", "kurang_bayar_bunga"),
    @("Proses Restrukturisasi", "Proses Cicilan"),
    # View/route path segments and class names in views
    @("pengajuan-restrukturisasi", "pengajuan-cicilan"),
    @("program-restrukturisasi", "penyesuaian-cicilan"),
    @("ar-perbulan", "laporan-tagihan-bulanan"),
    @("ar-performance", "monitoring-pembayaran"),
    @("debitur-piutang", "riwayat-tagihan"),
    @("report-pengembalian", "laporan-pengembalian")
)

# Process views (blade files)
$viewFiles = Get-ChildItem -Path "C:\Laragon\www\syifa-github\resources\views" -Filter "*.blade.php" -Recurse | Where-Object { $_.FullName -notmatch "Sfinlog|sfinlog|finlog" }

# Process routes
$routeFiles = Get-ChildItem -Path "C:\Laragon\www\syifa-github\routes" -Filter "*.php" | Where-Object { $_.Name -notmatch "sfinlog" }

# Process database seeders
$seederFiles = Get-ChildItem -Path "C:\Laragon\www\syifa-github\database\seeders" -Filter "*.php"

# Process config
$configFiles = Get-ChildItem -Path "C:\Laragon\www\syifa-github\config" -Filter "*.php"

$allFiles = @() + $viewFiles + $routeFiles + $seederFiles + $configFiles

$changedFiles = 0
foreach ($file in $allFiles) {
    $content = [System.IO.File]::ReadAllText($file.FullName, [System.Text.Encoding]::UTF8)
    $originalContent = $content
    foreach ($pair in $replacements) {
        $content = $content.Replace($pair[0], $pair[1])
    }
    if ($content -ne $originalContent) {
        [System.IO.File]::WriteAllText($file.FullName, $content, [System.Text.Encoding]::UTF8)
        $changedFiles++
        Write-Host "Updated: $($file.Name)"
    }
}
Write-Host "Total files changed: $changedFiles"
