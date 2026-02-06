# Panduan Perbaikan Error Deploy ke Azure

Error yang Anda alami (`Publish profile is invalid...`) disebabkan karena GitHub Actions Secret `AZURE_WEBAPP_PUBLISH_PROFILE` tidak valid.

Selain itu, jika Anda melihat error **"Basic authentication is disabled"** saat mencoba mendownload Publish Profile, Anda perlu mengaktifkannya terlebih dahulu.

## Langkah 1: Mengaktifkan Basic Authentication (Solusi "Basic authentication is disabled")

Agar tombol **Download publish profile** bisa berfungsi, Anda harus mengaktifkan Basic Auth di Azure:

1.  Buka **Azure Portal** dan masuk ke resource **App Service** Anda (`syfa-app`).
2.  Di menu sebelah kiri, cari bagian **Settings**, lalu klik **Configuration**.
3.  Klik tab **General settings**.
4.  Scroll ke bawah sampai menemukan bagian **Platform settings** atau **SCM Basic Auth Publishing Credentials**.
5.  Ubah opsi **SCM Basic Auth Publishing Credentials** menjadi **On**.
    *   *(Opsional)* Anda juga bisa mengaktifkan "FTP Basic Auth Publishing Credentials" jika perlu, tapi yang utama adalah **SCM**.
6.  Klik tombol **Save** di bagian atas dan tunggu proses selesai.

## Langkah 2: Download Profil Publikasi

Setelah Basic Auth aktif:

1.  Kembali ke halaman **Overview** App Service Anda.
2.  Klik tombol **Download publish profile** (atau **Get publish profile**).
3.  Sekarang file `.publishsettings` seharusnya berhasil terdownload.

## Langkah 3: Update GitHub Secret

1.  Buka file `.publishsettings` yang baru diunduh dengan Notepad atau VS Code.
2.  Salin **seluruh** teks di dalamnya.
3.  Buka repository GitHub ini > **Settings** > **Secrets and variables** > **Actions**.
4.  Edit secret `AZURE_WEBAPP_PUBLISH_PROFILE`.
5.  Tempel (Paste) isi file tadi ke kolom **Value**.
6.  Klik **Update secret**.

## Langkah 4: Deploy Ulang

1.  Masuk ke tab **Actions** di GitHub.
2.  Pilih workflow terakhir yang gagal.
3.  Klik **Re-run jobs** > **Re-run all jobs**.
