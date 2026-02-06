# Panduan Deployment Azure App Service

## Mengatasi Masalah Upload File Besar (Error 413) & Gambar Tidak Muncul (Error 404)

Repo ini telah dilengkapi dengan script otomatis untuk mengatasi masalah:
1.  **Error 413 (Entity Too Large)**: Batasan upload Nginx/PHP.
2.  **Error 404 (Not Found)**: Symbolic link storage yang hilang/rusak.

### Langkah Wajib: Update Startup Command

Agar perbaikan ini berjalan otomatis setiap kali aplikasi di-restart atau di-deploy, Anda **harus** memperbarui **Startup Command** di Azure Portal.

1.  Buka **Azure Portal** (https://portal.azure.com).
2.  Masuk ke App Service Anda (`syfa-app`).
3.  Di menu sebelah kiri, pilih **Configuration** -> **General settings**.
4.  Cari kolom **Startup Command**.
5.  Masukkan perintah berikut:

    ```bash
    sh /home/site/wwwroot/scripts/startup.sh
    ```

6.  Klik **Save**.
7.  Restart aplikasi Anda.

---

### Penjelasan Perbaikan

File `scripts/startup.sh` akan melakukan hal berikut setiap kali aplikasi berjalan:
1.  **Konfigurasi Nginx**: Menyalin `nginx.conf` (limit upload 100MB) dan me-reload server.
2.  **Storage Link**: Menjalankan `php artisan storage:link` untuk memastikan file upload bisa diakses publik.

### Konfigurasi Tambahan (Sudah Termasuk)

-   **`.user.ini`**: Mengatur limit PHP (`upload_max_filesize` = 100MB).
-   **`public/.htaccess`**: Fallback untuk Apache.
-   **`azure-deploy.yml`**: Deployment script diperbarui untuk menjaga struktur folder storage.
