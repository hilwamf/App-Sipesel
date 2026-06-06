# SIPESEL - Sistem Informasi Pembayaran Pajak Pasar
**Laravel 11** — Migrated from native PHP

## Fitur
- **Landing Page** — Halaman utama aplikasi
- **Auth** — Login (dengan Remember Me), Register, Logout
- **Pedagang** — Dashboard, Pembayaran (Harian/Mingguan/Bulanan + QRIS overlay), Riwayat bayar
- **Admin** — Dashboard, Verifikasi pembayaran, Manajemen user, Manajemen kios, Monitoring, Laporan (+ export CSV), Setting sistem
- **Pengawas** — Dashboard, Monitoring transaksi, Laporan jatuh tempo (+ WhatsApp link + kirim notifikasi)

## Akun Default (setelah seeder)
| Role      | Username   | Password     |
|-----------|------------|--------------|
| Admin     | admin      | admin123     |
| Pengawas  | pengawas   | pengawas123  |
| Pedagang  | pedagang1  | pedagang123  |
| Pedagang  | pedagang2  | pedagang123  |

## Setup

### 1. Konfigurasi Database
Edit file `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipesel
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Install dependencies
```bash
composer install
```

### 3. Generate App Key (jika belum ada)
```bash
php artisan key:generate
```

### 4. Jalankan Migrasi + Seeder
```bash
php artisan migrate --fresh --seed
```

### 5. Jalankan Server
```bash
php artisan serve
```

Buka browser ke: **http://localhost:8000**

## Struktur Folder
```
app/
  Http/
    Controllers/
      Admin/         → AdminController.php
      Pedagang/      → PedagangController.php
      Pengawas/      → PengawasController.php
      AuthController.php
    Middleware/
      RoleMiddleware.php
  Models/
    User, Transaksi, Notifikasi, Kios, Setting

resources/views/
  auth/       → login, register
  pedagang/   → dashboard, pembayaran, riwayat
  admin/      → dashboard, verifikasi, users, kios, monitoring, laporan, setting
  pengawas/   → dashboard, monitoring, laporan
  layouts/    → admin-header, pedagang-header, pengawas-header, auth
  welcome.blade.php

routes/web.php → semua route dengan middleware role
```
