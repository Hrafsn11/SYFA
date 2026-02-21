# Update permission names in ALL PHP files (controllers, seeder, etc.)
$replacements = @(
    # Menu permissions - bagi_hasil features
    @("sfinance.menu.ar_perbulan", "sfinance.menu.laporan_tagihan_bulanan"),
    @("sfinance.menu.ar_performance", "sfinance.menu.monitoring_pembayaran"),
    @("sfinance.menu.debitur_piutang", "sfinance.menu.riwayat_tagihan"),
    @("sfinance.menu.report_pengembalian", "sfinance.menu.laporan_pengembalian"),
    @("sfinance.menu.program_restukturisasi", "sfinance.menu.penyesuaian_cicilan"),
    @("sfinance.menu.program_restrukturisasi", "sfinance.menu.penyesuaian_cicilan"),
    @("sfinance.menu.pengajuan_restrukturisasi", "sfinance.menu.pengajuan_cicilan"),
    @("sfinlog.menu.ar_perbulan", "sfinlog.menu.laporan_tagihan_bulanan"),
    @("sfinlog.menu.ar_performance", "sfinlog.menu.monitoring_pembayaran"),
    @("sfinlog.menu.debitur_piutang", "sfinlog.menu.riwayat_tagihan"),
    @("sfinlog.menu.report_pengembalian", "sfinlog.menu.laporan_pengembalian"),
    # CRUD permissions
    @("pengajuan_restrukturisasi.view", "pengajuan_cicilan.view"),
    @("pengajuan_restrukturisasi.add", "pengajuan_cicilan.add"),
    @("pengajuan_restrukturisasi.edit", "pengajuan_cicilan.edit"),
    @("pengajuan_restrukturisasi.ajukan_restrukturisasi", "pengajuan_cicilan.ajukan_cicilan"),
    @("pengajuan_restrukturisasi.validasi_dokumen", "pengajuan_cicilan.validasi_dokumen"),
    @("pengajuan_restrukturisasi.persetujuan_ceo_ski", "pengajuan_cicilan.persetujuan_ceo_ski"),
    @("pengajuan_restrukturisasi.persetujuan_direktur", "pengajuan_cicilan.persetujuan_direktur"),
    @("program_restrukturisasi.view", "penyesuaian_cicilan.view"),
    @("program_restrukturisasi.add", "penyesuaian_cicilan.add"),
    @("program_restrukturisasi.edit", "penyesuaian_cicilan.edit"),
    @("program_restrukturisasi.edit_parameter", "penyesuaian_cicilan.edit_parameter"),
    @("program_restrukturisasi.upload", "penyesuaian_cicilan.upload"),
    @("program_restrukturisasi.konfirmasi", "penyesuaian_cicilan.konfirmasi"),
    @("program_restrukturisasi.generate_kontrak", "penyesuaian_cicilan.generate_kontrak"),
    @("debitur_piutang.edit", "riwayat_tagihan.edit"),
    @("ar_perbulan.view", "laporan_tagihan_bulanan.view"),
    @("ar_performance.view", "monitoring_pembayaran.view"),
    @("report_pengembalian.view", "laporan_pengembalian.view")
)

$basePath = "C:\Laragon\www\syifa-github"

# Files to update
$files = @()
$files += Get-ChildItem -Path "$basePath\app" -Filter "*.php" -Recurse | Where-Object { $_.FullName -notmatch "Sfinlog|sfinlog" }
$files += Get-ChildItem -Path "$basePath\database\seeders" -Filter "*.php"
$files += Get-ChildItem -Path "$basePath\routes" -Filter "*.php" | Where-Object { $_.Name -notmatch "sfinlog" }
$files += Get-ChildItem -Path "$basePath\resources\views" -Filter "*.blade.php" -Recurse | Where-Object { $_.FullName -notmatch "Sfinlog|sfinlog" }

$changedFiles = 0
foreach ($file in $files) {
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
