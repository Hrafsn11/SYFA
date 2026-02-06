# Panduan Perbaikan Error Deploy ke Azure

Error yang Anda alami (`Publish profile is invalid...`) kemungkinan besar disebabkan karena GitHub Actions Secret `AZURE_WEBAPP_PUBLISH_PROFILE` kosong atau berisi data yang tidak valid (seperti teks "REDACTED" yang ada di file `azure-publish-profile.xml`).

File `azure-publish-profile.xml` di dalam repository ini **tidak bisa digunakan** untuk deployment karena password-nya telah disensor ("REDACTED").

Untuk memperbaiki error ini, Anda harus memperbarui GitHub Secret dengan kredensial yang valid.

## Langkah-langkah Perbaikan

1.  **Download Profil Publikasi Baru**:
    *   Masuk ke [Azure Portal](https://portal.azure.com).
    *   Buka resource App Service Anda yang bernama `syfa-app`.
    *   Pada halaman **Overview**, klik tombol **Get publish profile** (atau **Download publish profile**) di menu bagian atas.
    *   Ini akan mengunduh file berekstensi `.publishsettings`.

2.  **Salin Isi Profil**:
    *   Buka file yang baru saja diunduh menggunakan teks editor (seperti Notepad atau VS Code).
    *   Salin **seluruh** isi teks di dalam file tersebut.

3.  **Update GitHub Secret**:
    *   Buka halaman repository ini di GitHub.
    *   Klik tab **Settings** > **Secrets and variables** > **Actions**.
    *   Cari secret bernama `AZURE_WEBAPP_PUBLISH_PROFILE`.
    *   Klik ikon **Edit** (gambar pensil). Jika belum ada, klik **New repository secret** dan beri nama `AZURE_WEBAPP_PUBLISH_PROFILE`.
    *   **Tempel (Paste)** isi file publish profile yang sudah disalin ke kolom **Value**.
    *   Klik **Update secret** (atau **Add secret**).

4.  **Coba Deploy Ulang**:
    *   Buka tab **Actions** di GitHub.
    *   Pilih workflow yang gagal terakhir kali.
    *   Klik tombol **Re-run jobs** > **Re-run all jobs**.

Setelah langkah ini, deployment seharusnya berhasil jika tidak ada masalah lain pada kode aplikasi.
