# Selenium Tests (Python) — Sewain

Automated **browser testing** untuk aplikasi Sewain menggunakan **Selenium + pytest**.
Test ini menyetir Chrome sungguhan untuk menguji aplikasi seperti yang dilihat user
(berbeda dengan PHPUnit Feature test yang tidak membuka browser).

## Prasyarat

- **Python** >= 3.10
- **Google Chrome** terpasang (ChromeDriver diurus otomatis oleh Selenium Manager)
- Aplikasi Sewain **sedang berjalan** dan dapat diakses (mis. `http://localhost:8000`)

## Struktur

```
selenium-tests/
├── requirements.txt      # dependency Python
├── .env.example          # contoh konfigurasi (salin jadi .env)
├── pytest.ini            # konfigurasi pytest
├── config.py             # baca BASE_URL, kredensial, dll dari env
├── conftest.py           # fixture WebDriver + screenshot saat gagal
├── pages/                # Page Object Model
│   ├── base_page.py
│   ├── home_page.py
│   ├── login_page.py
│   └── catalog_page.py
└── tests/
    ├── test_home.py      # beranda & kategori
    ├── test_auth.py      # login user & vendor (sukses/gagal)
    └── test_catalog.py   # daftar barang & filter
```

## Setup

Jalankan perintah berikut dari folder **`selenium-tests/`**.

### 1. Siapkan data uji di aplikasi (sekali saja)

Dari root project Laravel, buat akun & barang uji yang dipakai test:

```bash
php artisan db:seed --class=SeleniumSeeder
```

Ini membuat (idempotent, aman diulang):

| Peran  | Email                  | Password   |
| ------ | ---------------------- | ---------- |
| User   | `user@selenium.test`   | `password` |
| Vendor | `vendor@selenium.test` | `password` |

plus 3 barang siap sewa.

> ⚠️ Browser test menulis ke database yang dipakai aplikasi. Sebaiknya arahkan
> aplikasi ke **database khusus** (mis. `sewain_dusk`) agar data dev tidak tercampur.

### 2. Pastikan aplikasi berjalan

```bash
php artisan serve        # http://localhost:8000
```

### 3. Buat virtual environment & install dependency

```bash
# Windows (PowerShell)
python -m venv .venv
.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

```bash
# macOS / Linux
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
```

### 4. Konfigurasi (opsional)

Default sudah menunjuk ke `http://localhost:8000`. Untuk mengubah, salin dan edit `.env`:

```bash
cp .env.example .env      # Windows: Copy-Item .env.example .env
```

## Menjalankan Test

```bash
# Semua test (headless)
pytest

# Lihat browser-nya (non-headless)
# Windows:  $env:SELENIUM_HEADLESS="false"; pytest
# Linux/mac: SELENIUM_HEADLESS=false pytest

# Hanya alur paling penting
pytest -m smoke

# Satu file / satu test
pytest tests/test_auth.py
pytest tests/test_auth.py::test_user_login_berhasil
```

Jika ada test yang gagal, screenshot otomatis disimpan di folder `screenshots/`.

## Catatan

- **Selector**: test memakai atribut `name` form, teks tombol, dan nama barang seed
  agar stabil. Untuk ketahanan ekstra, tambahkan atribut `data-test="..."` pada
  elemen Blade lalu pakai `By.CSS_SELECTOR, "[data-test='...']"`.
- **Dua guard auth**: User memakai Breeze (`/login`), Vendor memakai guard custom
  (`/vendor/login`) — keduanya diuji terpisah di `test_auth.py`.
- Test ini **standalone**: tidak mengubah kode aplikasi, hanya mengaksesnya lewat HTTP.
