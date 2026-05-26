# Sewain — RentalApp

Aplikasi rental online berbasis Laravel 13 dengan dua peran (User & Vendor). Mendukung booking, pembayaran, verifikasi pengembalian, chat antar pengguna, dan sistem review dua arah.

---

## Daftar Isi

- [Fitur](#fitur)
- [Tech Stack](#tech-stack)
- [Prasyarat](#prasyarat)
- [Instalasi](#instalasi)
- [Workflow Aplikasi](#workflow-aplikasi)
- [Menjalankan Tes](#menjalankan-tes)
- [Troubleshooting](#troubleshooting)
- [Lisensi](#lisensi)

---

## Fitur

- **Autentikasi terpisah** untuk User (penyewa) dan Vendor (pemilik barang)
- **Manajemen Item** oleh vendor (tambah / edit / toggle ketersediaan)
- **Booking & checkout** dengan cek tabrakan tanggal (overlap)
- **Pembayaran** (status: pending / paid / failed) dengan auto-cancel rental pending yang overlap saat ada pembayaran sukses
- **Deposit dinamis** — nilai deposit menyesuaikan rating user (semakin tinggi rating, semakin kecil deposit)
- **Pengembalian barang** dengan verifikasi dari sisi vendor
- **Review dua arah** — user me-review vendor, vendor me-review user
- **Chat** antara user dan vendor (Conversation & Message)
- **Notifikasi otomatis** rental jatuh tempo / overdue via scheduled command
- Dashboard terpisah untuk user dan vendor

---

## Tech Stack

| Layer       | Teknologi                                  |
| ----------- | ------------------------------------------ |
| Backend     | PHP 8.3, Laravel 13                        |
| Autentikasi | Laravel Breeze v2                          |
| Frontend    | Blade, Alpine.js 3, Tailwind CSS 3, Vite 8 |
| Database    | MySQL 8 (default) / SQLite                 |
| Testing     | PHPUnit 12                                 |

---

## Prasyarat

Sebelum mulai, pastikan sudah terpasang:

- **PHP** >= 8.3 (dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd`)
- **Composer** >= 2.6
- **Node.js** >= 20 dan **npm** >= 10
- **MySQL** >= 8.0 (atau SQLite)
- **Git**

> Proyek ini dikembangkan menggunakan **Laragon** di Windows, tetapi dapat berjalan di macOS / Linux dengan setup serupa.

---

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/Federico2884/Sewain.git
cd Sewain
```

### 2. Install dependency PHP

```bash
composer install
```

### 3. Install dependency JavaScript

```bash
npm install
```

### 4. Salin file environment

Windows (PowerShell):

```powershell
Copy-Item .env.example .env
```

macOS / Linux:

```bash
cp .env.example .env
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Konfigurasi database

Buka file `.env` dan sesuaikan kredensial database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rentalapp
DB_USERNAME=root
DB_PASSWORD=
```

Buat database kosong dengan nama `rentalapp` terlebih dahulu (via phpMyAdmin, HeidiSQL, atau jalankan `CREATE DATABASE rentalapp;`).

### 7. Jalankan migrasi

```bash
php artisan migrate
```

### 8. Symlink storage

```bash
php artisan storage:link
```

### 9. Build asset frontend

```bash
# Mode development (hot reload)
npm run dev

# Mode production
npm run build
```

### 10. Jalankan aplikasi

Buka dua terminal:

```bash
# Terminal 1 — Laravel server
php artisan serve

# Terminal 2 — Vite dev server
npm run dev
```

Atau jalankan keduanya sekaligus dengan satu perintah:

```bash
composer run dev
```

Aplikasi akan tersedia di **http://localhost:8000**.

---

## Workflow Aplikasi

### Alur Pengguna (User / Penyewa)

```
Daftar / Login
    │
    ▼
Browse katalog barang  ──►  Lihat detail barang
    │
    ▼
Klik "Sewa" → Pilih tanggal mulai, durasi, unit (jam/hari/bulan), metode (pickup/delivery)
    │
    ▼
Checkout → Pilih metode pembayaran → Konfirmasi
    │
    ▼
Pembayaran sukses → Rental aktif (status: paid)
    │
    ▼
Setelah masa sewa selesai → Klik "Kembalikan"  (status: returning, menunggu verifikasi vendor)
    │
    ▼
Vendor verifikasi → Rental selesai
    │
    ▼
User dapat memberi review kepada vendor
```

### Alur Vendor

```
Daftar / Login Vendor
    │
    ▼
Dashboard → Statistik rental aktif & pendapatan
    │
    ▼
Kelola Item (CRUD) → Set harga, deposit, foto, ketersediaan
    │
    ▼
Terima pesanan → Pantau rental masuk (paid)
    │
    ▼
Saat user meminta pengembalian → Verifikasi barang  (cek kondisi)
    │
    ▼
Beri review kepada user
```

### Status Rental

| Status         | Arti                                                                |
| -------------- | ------------------------------------------------------------------- |
| **pending**    | Rental dibuat, pembayaran belum berhasil                            |
| **paid**       | Pembayaran sukses, rental aktif                                     |
| **failed**     | Pembayaran gagal / dibatalkan otomatis karena overlap dengan paid   |
| **active**     | Sudah dibayar dan belum dikembalikan / belum diverifikasi vendor    |
| **due soon**   | Aktif & akan jatuh tempo dalam <= 2 hari (memicu notifikasi)        |
| **overdue**    | Aktif & sudah melewati tanggal pengembalian                         |
| **returning**  | User sudah klik "Kembalikan", menunggu verifikasi vendor            |
| **completed**  | Vendor sudah verifikasi pengembalian                                |

### Fitur Pendukung

- **Chat** — User dapat memulai percakapan dengan vendor dari halaman detail barang. Vendor dapat membalas dari panel `Vendor → Chats`.
- **Notifikasi otomatis** — Command `php artisan rentals:remind` (scheduled) mengirim notifikasi `RentalDueSoonNotification` dan `RentalOverdueNotification`.
- **Deposit dinamis** — Method `User::depositMultiplier()` menyesuaikan deposit berdasarkan rating rata-rata user.
- **Cegah overlap** — Sistem memvalidasi tanggal booking agar tidak tabrakan dengan rental aktif lain (lihat `Item::overlapsBookedDates`).

---

## Menjalankan Tes

```bash
# Semua tes
php artisan test --compact

# File tertentu
php artisan test --compact tests/Feature/BookingOverlapTest.php

# Filter berdasarkan nama
php artisan test --compact --filter=testName
```

---

## Troubleshooting

**`Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest`**

Jalankan build asset:

```bash
npm run build
```

**Permission denied pada `storage/` atau `bootstrap/cache/`** (macOS / Linux)

```bash
chmod -R 775 storage bootstrap/cache
```

**Error koneksi database**

Pastikan service MySQL berjalan dan kredensial di `.env` sudah benar.

**Lupa generate `APP_KEY`**

```bash
php artisan key:generate
```

**Gambar item tidak muncul**

Pastikan sudah menjalankan `php artisan storage:link`.

---

## Lisensi

Proyek ini menggunakan lisensi **MIT**.
