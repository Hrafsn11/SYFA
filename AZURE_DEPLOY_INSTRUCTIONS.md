# Panduan Deployment Azure App Service

## Mengatasi Masalah Upload File Besar (Error 413 Request Entity Too Large)

Jika Anda mengalami error `413 Request Entity Too Large` saat mengupload file (seperti dokumen peminjaman), hal ini biasanya disebabkan oleh batasan ukuran upload pada Web Server (Nginx/Apache) atau PHP di Azure App Service.

Repo ini telah diperbarui dengan file konfigurasi berikut untuk mengatasi masalah tersebut:

1.  **`.user.ini`** (di root dan `public/`): Mengatur `upload_max_filesize` dan `post_max_size` PHP menjadi 100MB.
2.  **`public/.htaccess`**: Mengatur `LimitRequestBody` dan limit PHP untuk server yang menggunakan Apache.
3.  **`nginx.conf`**: Konfigurasi Nginx kustom dengan `client_max_body_size 100M`.

### Langkah Penting untuk Pengguna Nginx (Default PHP 8.2 Linux)

Jika aplikasi Anda menggunakan **PHP 8.2 di Linux (Azure App Service)**, secara default menggunakan Nginx yang memiliki batasan upload 1MB. File `nginx.conf` di root repo ini **tidak otomatis digunakan** kecuali Anda mengonfigurasinya.

Anda perlu menambahkan **Startup Command** di Azure Portal agar konfigurasi Nginx ini digunakan.

#### Cara Mengatur Startup Command:

1.  Buka **Azure Portal** (https://portal.azure.com).
2.  Masuk ke App Service Anda (`syfa-app`).
3.  Di menu sebelah kiri, pilih **Configuration** -> **General settings**.
4.  Cari kolom **Startup Command**.
5.  Masukkan perintah berikut:

    ```bash
    cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default && service nginx reload
    ```

6.  Klik **Save**.
7.  Restart aplikasi Anda.

> **Catatan:** Perintah di atas menyalin konfigurasi Nginx dari repo ke sistem dan me-reload Nginx. Jika aplikasi gagal start setelah ini, coba hapus Startup Command dan simpan kembali untuk kembali ke default, lalu cek log error.
