# Tutorial Menjalankan Aplikasi Manajemen Katalog

Panduan ini menjelaskan langkah-langkah untuk menjalankan **Aplikasi Manajemen Katalog Toko Sepatu** di komputer lokal.

## Tentang Aplikasi

Aplikasi ini terdiri dari dua bagian:

| Komponen | Teknologi | Peran |
|----------|-----------|-------|
| **Frontend** | Laravel (PHP) + Blade | Antarmuka web untuk pegawai/admin |
| **Backend API** | Node.js + Express + Sequelize | REST API dan penyimpanan data utama (MySQL) |

Frontend berjalan di `http://localhost:8000` dan memanggil API backend di `http://localhost:5000`.

---

## Prasyarat

Pastikan perangkat lunak berikut sudah terinstal:

- **PHP** ≥ 8.2
- **Composer** — manajer dependensi PHP
- **Node.js** ≥ 18 (termasuk npm)
- **MySQL** — server database untuk backend API
- **Git** (opsional, untuk clone repository)

Periksa versi dengan perintah:

```bash
php -v
composer -V
node -v
npm -v
mysql --version
```

---

## 1. Clone / Unduh Proyek

Jika belum memiliki kode proyek:

```bash
git clone <url-repository>
cd Aplikasi_Manajemen_Katalog
```

---

## 2. Setup Database MySQL (Backend)

Backend API menggunakan MySQL. Buat database baru melalui MySQL CLI, phpMyAdmin, atau alat database lainnya.

```sql
CREATE DATABASE katalog_sepatu_db;
```

Catat kredensial database Anda (host, username, password, nama database) — akan dipakai di langkah berikutnya.

---

## 3. Setup Backend (Node.js)
### 3.1 Instal dependensi

```bash
cd backend
npm install
```

### 3.2 Buat file konfigurasi `.env`

Buat file `backend/.env` dengan isi berikut 

```env
PORT=5000
DB_HOST=127.0.0.1
DB_USER=root
DB_PASSWORD=
DB_NAME=katalog_sepatu_db
DB_DIALECT=mysql
JWT_SECRET=rahasia_jwt_super_aman_123
```

### 3.3 Jalankan backend

```bash
npm run dev
```

Atau tanpa auto-reload:

```bash
node index.js
```

Jika berhasil, terminal akan menampilkan pesan seperti:

```
MySQL connected successfully.
Database synchronized (tables dropped and recreated)
Default sizes seeded
Server is running on port 5000
```

Backend API siap diakses di **http://localhost:5000**.

> **Catatan:** Saat backend dijalankan, tabel database akan **dibuat ulang otomatis** (`force: true`). Data lama akan terhapus setiap kali server backend di-restart.

### 3.4 Akun admin default

Saat backend pertama kali berjalan, akun admin dibuat otomatis:

| Field | Nilai |
|-------|-------|
| Username | `admin` |
| Password | `password123` |

### 3.5 (Opsional) Seed kategori awal

Untuk menambahkan kategori default (Boots, Running, Sneakers), jalankan di terminal terpisah (backend harus sudah pernah dijalankan minimal sekali):

```bash
cd backend
node seedCategories.js
```

---

## 4. Setup Frontend (Laravel)

Buka terminal **baru** dan kembali ke folder root proyek (bukan folder `backend`).

### 4.1 Instal dependensi PHP

```bash
composer install
```

### 4.2 Buat file `.env` Laravel

```bash
copy .env.example .env
```

Di Linux/macOS:

```bash
cp .env.example .env
```

### 4.3 Generate application key

```bash
php artisan key:generate
```

### 4.4 Siapkan database Laravel (SQLite)

Laravel membutuhkan database lokal untuk session dan cache. Buat file SQLite:

```bash
# Windows (PowerShell)
New-Item -Path database\database.sqlite -ItemType File -Force

# Linux / macOS
touch database/database.sqlite
```

Jalankan migrasi:

```bash
php artisan migrate
```

> Konfigurasi default di `.env.example` sudah menggunakan `DB_CONNECTION=sqlite`. Tidak perlu mengubahnya kecuali Anda ingin memakai MySQL juga untuk Laravel.

### 4.5 (Opsional) Instal dependensi frontend assets

Hanya diperlukan jika Anda ingin mengembangkan asset Vite/Tailwind:

```bash
npm install
```

Untuk menjalankan aplikasi sehari-hari, langkah ini **tidak wajib** karena tampilan Blade sudah memakai Bootstrap dari CDN.

---

## 5. Menjalankan Aplikasi

Aplikasi membutuhkan **dua proses** yang berjalan bersamaan.

### Terminal 1 — Backend API

```bash
cd backend
npm run dev
```

### Terminal 2 — Frontend Laravel

```bash
php artisan serve
```

Frontend akan tersedia di **http://localhost:8000**.

---

## 6. Menggunakan Aplikasi

1. Buka browser dan akses **http://localhost:8000**
2. Anda akan melihat halaman **Login Sistem**
3. Masuk dengan kredensial default:
   - **Username:** `admin`
   - **Password:** `password123`
4. Setelah login, Anda dapat mengakses:
   - **Dashboard** — ringkasan data
   - **Katalog Produk** — daftar dan pencarian produk
   - **Kategori Sepatu** — kelola kategori
   - **Manajemen Merek** — kelola merek sepatu
   - **Tambah Produk** — tambah produk baru beserta varian ukuran

---

## 7. Endpoint API Utama

Backend menyediakan API REST di bawah prefix `/api`:

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| POST | `/api/auth/login` | Login pengguna |
| POST | `/api/auth/register` | Registrasi pengguna baru |
| GET | `/api/catalog` | Daftar produk |
| POST | `/api/catalog` | Tambah produk |
| GET | `/api/categories` | Daftar kategori |
| GET | `/api/brands` | Daftar merek |
| GET | `/api/sizes` | Daftar ukuran sepatu |

Uji koneksi API dengan membuka **http://localhost:5000** — halaman akan menampilkan `API is running...`.

---

## 8. Troubleshooting

### Backend gagal connect ke MySQL

- Pastikan layanan MySQL sudah berjalan
- Periksa `DB_HOST`, `DB_NAME`, `DB_USER`, dan `DB_PASSWORD` di `backend/.env`
- Pastikan database sudah dibuat (`CREATE DATABASE ...`)

### Login gagal / "Username atau password salah"

- Pastikan backend berjalan di port **5000**
- Restart backend agar akun admin ter-seed ulang
- Gunakan username `admin` dan password `password123`

### Halaman Laravel error saat dibuka

- Jalankan `php artisan key:generate` jika belum
- Jalankan `php artisan migrate` jika database SQLite belum ada
- Periksa versi PHP (minimal 8.2)

### Data produk/kategori hilang setelah restart backend

- Ini normal karena backend menggunakan `sequelize.sync({ force: true })` yang me-reset tabel setiap startup
- Tambahkan kembali data melalui antarmuka web, atau jalankan ulang `node seedCategories.js` untuk kategori

### Frontend tidak bisa memanggil API (CORS / network error)

- Pastikan backend sudah berjalan sebelum membuka halaman Laravel
- Frontend memanggil API ke `http://localhost:5000` — jangan ubah port backend tanpa menyesuaikan file Blade di `resources/views/`

### Port sudah dipakai

Ganti port Laravel:

```bash
php artisan serve --port=8080
```

Ganti port backend dengan mengubah `PORT` di `backend/.env`.

---

## 9. Ringkasan Perintah Cepat

```bash
# Backend
cd backend
npm install
# buat backend/.env terlebih dahulu
npm run dev

# Frontend (terminal terpisah, dari root proyek)
composer install
copy .env.example .env          # Windows
php artisan key:generate
New-Item database\database.sqlite -ItemType File -Force   # Windows
php artisan migrate
php artisan serve
```

---

## 10. Struktur Folder Penting

```
Aplikasi_Manajemen_Katalog/
├── backend/                 # API Node.js + Express
│   ├── config/db.js         # Konfigurasi koneksi MySQL
│   ├── controllers/         # Logika bisnis API
│   ├── models/              # Model Sequelize
│   ├── routes/              # Definisi route API
│   ├── index.js             # Entry point backend
│   └── .env                 # Konfigurasi backend (buat manual)
├── resources/views/         # Halaman Blade (frontend)
├── routes/web.php           # Route halaman Laravel
├── public/css/              # Stylesheet
└── .env                     # Konfigurasi Laravel
```

---


