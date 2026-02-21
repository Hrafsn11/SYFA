# Additional replacements for view path strings in PHP files
$replacements = @(
    @("livewire.ar-perbulan", "livewire.laporan-tagihan-bulanan"),
    @("livewire.ar-performance", "livewire.monitoring-pembayaran"),
    @("livewire.debitur-piutang", "livewire.riwayat-tagihan"),
    @("livewire.pengajuan-restrukturisasi", "livewire.pengajuan-cicilan"),
    @("livewire.program-restrukturisasi", "livewire.penyesuaian-cicilan"),
    @("livewire.report-pengembalian", "livewire.laporan-pengembalian"),
    @("program-restrukturisasi.create", "penyesuaian-cicilan.create"),
    @("'ar-perbulan'", "'laporan-tagihan-bulanan'"),
    @('"ar-perbulan"', '"laporan-tagihan-bulanan"'),
    @("'ar-performance'", "'monitoring-pembayaran'"),
    @('"ar-performance"', '"monitoring-pembayaran"'),
    @("'debitur-piutang'", "'riwayat-tagihan'"),
    @('"debitur-piutang"', '"riwayat-tagihan"'),
    @("'pengajuan-restrukturisasi'", "'pengajuan-cicilan'"),
    @('"pengajuan-restrukturisasi"', '"pengajuan-cicilan"'),
    @("'program-restrukturisasi'", "'penyesuaian-cicilan'"),
    @('"program-restrukturisasi"', '"penyesuaian-cicilan"'),
    @("'report-pengembalian'", "'laporan-pengembalian'"),
    @('"report-pengembalian"', '"laporan-pengembalian"'),
    # Namespace paths in Livewire
    @("App\\Livewire\\ArPerbulan", "App\\Livewire\\LaporanTagihanBulanan"),
    @("App\\Livewire\\ArPerformance", "App\\Livewire\\MonitoringPembayaran"),
    @("App\\Livewire\\DebiturPiutang", "App\\Livewire\\RiwayatTagihan"),
    @("App\\Livewire\\ReportPengembalian", "App\\Livewire\\LaporanPengembalian")
)

$basePath = "C:\Laragon\www\syifa-github\app"
$files = Get-ChildItem -Path $basePath -Filter "*.php" -Recurse | Where-Object { $_.FullName -notmatch "Sfinlog|sfinlog" }

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
