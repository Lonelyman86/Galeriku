# Panduan Deployment ke Vercel

Panduan ini akan membantu Anda men-deploy aplikasi Laravel "Galeriku" ke Vercel.

## 1. Persiapan Database (PENTING)

Vercel tidak memiliki database. Anda harus menyewa database cloud terpisah.
Rekomendasi gratis:

-   **Neon** (Postgres)
-   **Aiven** (MySQL)
-   **Supabase** (Postgres)
-   **PlanetScale** (MySQL)

Setelah membuat database, catat credential berikut: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

## 2. Persiapan Storage (PENTING)

File yang diupload ke Vercel akan hilang saat deployment baru. Gunakan **Cloudinary** untuk menyimpan gambar.

1. Daftar di [Cloudinary](https://cloudinary.com/).
2. Ambil `Cloud Name`, `API Key`, dan `API Secret` dari Dashboard.

## 3. Environment Variables di Vercel

Saat membuat project baru di Vercel, masuk ke **Settings > Environment Variables** dan tambahkan:

| Variable                | Value (Contoh)                                                 |
| :---------------------- | :------------------------------------------------------------- |
| `APP_ENV`               | `production`                                                   |
| `APP_KEY`               | (Copy dari .env lokal Anda, `php artisan key:generate --show`) |
| `APP_DEBUG`             | `false` (atau `true` jika debugging)                           |
| `APP_URL`               | `https://nama-project-anda.vercel.app`                         |
| `DB_CONNECTION`         | `mysql` (atau `pgsql`)                                         |
| `DB_HOST`               | `aws.connect.psdb.cloud`                                       |
| `DB_PORT`               | `3306`                                                         |
| `DB_DATABASE`           | `galeriku_db`                                                  |
| `DB_USERNAME`           | `user_xyz`                                                     |
| `DB_PASSWORD`           | `password123`                                                  |
| `FILESYSTEM_DISK`       | `cloudinary`                                                   |
| `CLOUDINARY_CLOUD_NAME` | `galeriku-cloud`                                               |
| `CLOUDINARY_API_KEY`    | `123456789`                                                    |
| `CLOUDINARY_API_SECRET` | `abcdefgh`                                                     |

## 4. Cara Deploy

1. **Push ke GitHub**: Pastikan semua kode sudah di-push ke repository GitHub Anda.
2. **Login ke Vercel**: Buka [vercel.com](https://vercel.com) dan login dengan GitHub.
3. **Add New Project**: Pilih repository `Galeriku`.
4. **Configure**: Masukkan Environment Variables di atas.
5. **Deploy**: Klik tombol Deploy.

## Catatan Tambahan

-   Folder `storage/` lokal tidak akan berfungsi untuk upload, itulah kenapa kita wajib pakai Cloudinary.
-   Jika ada error `HTTP 500`, cek logs di dashboard Vercel (tab "Logs").

## 5. Konfigurasi Local Development

Agar aplikasi tetap berjalan normal di komputer Anda (Localhost), pastikan file `.env` Anda menggunakan konfigurasi berikut:

`FILESYSTEM_DISK=public`

Ini akan membuat aplikasi menggunakan storage lokal (`storage/app/public`) saat sedang develop, sementara di Vercel nanti akan otomatis menggunakan Cloudinary sesuai env var di dashboard Vercel.
