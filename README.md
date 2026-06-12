
kelompok Manajemen Katalog Produk
Anggota Kelompok:
Dimas Wahyu Sejati (5230411260) 
Rizki Dimas Sasongko (5230411263) 
Ahsan 

# Aplikasi Manajemen Katalog Toko Sepatu

Aplikasi web untuk manajemen katalog produk toko sepatu dengan fitur CRUD produk, kategori, merek, dan autentikasi. Proyek ini dibangun dengan arsitektur terpisah antara *Backend* (API) dan *Frontend* (UI).

## Arsitektur & Teknologi

| Komponen | Peran | Teknologi Utama | Port Default |
|---|---|---|---|
| **Backend** | Menyediakan REST API dan mengelola database | Node.js, Express.js, Sequelize ORM, MySQL | `5000` |
| **Frontend** | Antarmuka pengguna (UI) yang dirender di server | PHP, Laravel (Blade), Bootstrap 5, Vanilla JS | `8000` |

---

## Cara Menjalankan Aplikasi

Karena aplikasi ini terdiri dari dua layanan terpisah, Anda harus menjalankan keduanya secara bersamaan di terminal yang berbeda.

### Prasyarat
1. **Node.js** (v14 atau lebih baru)
2. **PHP** (v8.2 atau lebih baru)
3. **Composer**
4. **MySQL Server** (XAMPP, Laragon, dll.)

### 1. Konfigurasi Database (Backend)
1. Buka aplikasi MySQL (misal: phpMyAdmin).
2. Buat database baru bernama `katalog_sepatu` (atau nama lain sesuai pilihan Anda).
3. Duplikat/buat file `.env` di dalam folder `backend/` dan sesuaikan kredensialnya:
   ```env
   PORT=5000
   DB_HOST=localhost
   DB_USER=root
   DB_PASSWORD=
   DB_NAME=katalog_sepatu
   DB_DIALECT=mysql
   JWT_SECRET=rahasia_jwt_super_aman_123
   ```

### 2. Menjalankan Backend (Terminal 1)
Buka terminal baru, lalu jalankan perintah berikut:
```bash
cd backend
npm install
npm run dev
```
> **Catatan:** Saat pertama kali dijalankan, Sequelize akan otomatis membuat tabel di database dan mengisi data *dummy* (ukuran sepatu, kategori awal, dan akun admin default). Server akan berjalan di `http://localhost:5000`.

### 3. Menjalankan Frontend (Terminal 2)
Buka terminal baru lainnya, lalu jalankan perintah berikut:
```bash
cd frontend
composer install
npm install
php artisan serve
```
> **Catatan:** Frontend akan berjalan di `http://localhost:8000`. Buka alamat tersebut di browser Anda. Akun login default (jika belum diubah) adalah username: `admin`, password: `password123`.

---

## Struktur Folder & Penjelasan File

Aplikasi ini dibagi menjadi dua folder utama di direktori root: `backend/` dan `frontend/`.

### 📁 `backend/` (Express.js API)
Folder ini berisi seluruh logika server, manipulasi database, dan penyediaan rute API.

- **`config/`**
  - `db.js`: Konfigurasi koneksi database MySQL menggunakan Sequelize ORM.
- **`controllers/`** *(Logika Bisnis)*
  - `authController.js`: Menangani proses login dan registrasi (JWT token).
  - `brandController.js`: Menangani operasi CRUD untuk data Merek (Brand).
  - `catalogController.js`: Menangani operasi CRUD kompleks untuk Produk Sepatu (termasuk variasi ukuran dan stok).
  - `categoryController.js`: Menangani operasi CRUD untuk data Kategori.
  - `sizeController.js`: Menyediakan daftar ukuran sepatu yang valid.
- **`helpers/`** *(Fungsi Bantuan)*
  - `productHelper.js`: Berisi fungsi terpusat untuk menghitung stok total sepatu berdasarkan variannya dan membuat query include standar, untuk menghindari duplikasi kode antar controller.
- **`middleware/`**
  - `authMiddleware.js`: Memvalidasi token JWT untuk memproteksi rute yang bersifat rahasia (harus login).
  - `errorHandler.js`: Penanganan error terpusat (Centralized Error Handling) yang menangkap *exception* dan mengembalikan respon JSON yang rapi.
- **`models/`** *(Skema Database)*
  - `Brand.js`, `Category.js`, `Product.js`, `ProductVariant.js`, `Size.js`, `User.js`: Definisi skema tabel database Sequelize.
  - `associations.js`: Tempat terpusat untuk mendefinisikan hubungan antar tabel (misal: *Product hasMany ProductVariant*).
- **`routes/`**
  - Kumpulan file (seperti `catalogRoutes.js`) yang memetakan URL endpoint API ke fungsi di dalam *controllers*.
- **`seeders/`**
  - `seeder.js`: Skrip otomatis yang memasukkan data awal (ukuran sepatu EU, kategori default, akun admin) saat server pertama kali dinyalakan.
- **`index.js`**
  - File utama (Entry Point) dari aplikasi backend. Berfungsi menjalankan server Express, mendaftarkan *middleware*, me-*load* *routes*, dan menyambungkan ke database.

### 📁 `frontend/` (Laravel UI)
Folder ini berisi antarmuka pengguna berbasis web yang mengkonsumsi API dari backend.

- **`app/`, `bootstrap/`, `config/`, `database/`, `storage/`, dll.**
  - Folder bawaan standar dari framework Laravel.
- **`routes/`**
  - `web.php`: Mendefinisikan URL halaman web (seperti `/dashboard`, `/katalog`) dan mengarahkannya ke file tampilan (view) yang sesuai. Tidak berisi logika pengolahan data karena semua data ditarik dari API menggunakan JavaScript di sisi klien.
- **`resources/views/`** *(Tampilan HTML/Blade)*
  - `layouts/app.blade.php`: *Template/kerangka* utama web yang memuat sidebar, header (navbar), dan *scripts* bawaan (Bootstrap, SweetAlert). Halaman lain akan dimasukkan ke dalam kerangka ini.
  - `login.blade.php`: Halaman login awal.
  - `dashboard.blade.php`: Halaman ringkasan statistik dan daftar produk cepat.
  - `katalog.blade.php`: Halaman utama untuk melihat produk sepatu dengan fitur pencarian, filter (kategori, merek, ukuran), dan pengurutan (harga, stok).
  - `kategori.blade.php` & `merek.blade.php`: Halaman untuk mengelola data master kategori dan merek.
  - `tambah-produk.blade.php` & `edit-produk.blade.php`: Formulir input data sepatu beserta konfigurasi stok per ukuran.
  - `detail-produk.blade.php`: Halaman rincian spesifik dari satu pasang sepatu.
- **`public/`** *(Aset Statis & Skrip Klien)*
  - **`css/`**: Berisi file CSS kustom untuk memberikan gaya (styling) tambahan di luar Bootstrap.
  - **`js/`** *(Logika Sisi Klien / Frontend)*
    - `api-config.js`: File sentral untuk menyimpan *Base URL API* (`http://localhost:5000`). Jika backend pindah server/port, cukup ubah di satu file ini.
    - `helpers.js`: Kumpulan fungsi JavaScript yang dipakai berulang di berbagai halaman, seperti `formatCurrency` (format rupiah), merender *badge* stok sepatu, menampilkan *SweetAlert*, dan logika dropdown.
    - `script.js`: Berisi logika global atau event listener.

---

## Fitur Utama

- 🔐 **Autentikasi JWT**: Login aman berbasis token yang disimpan di `localStorage` peramban.
- 📦 **CRUD Produk Majemuk**: Manajemen data sepatu tidak sekadar nama/harga, namun mendukung variasi ukuran sepatu (EU 36-45) lengkap dengan perhitungan stok independen di tiap ukurannya.
- 🏷️ **Data Master Terpisah**: Pengelolaan Kategori dan Merek sebagai entitas terpisah untuk kemudahan klasifikasi data.
- 🔍 **Filter & Pencarian Dinamis**: Halaman katalog yang interaktif untuk memfilter pencarian berdasarkan ukuran, merek, kategori, dan *sorting* secara *real-time* di sisi klien.
- 🎨 **UI Modern & Bersih**: Menggunakan Bootstrap 5, ikon FontAwesome, dialog interaktif SweetAlert2, dan gaya visual (CSS) kustom yang elegan.

---

## Panduan Testing API (Postman / Thunder Client)

Aplikasi ini menggunakan arsitektur REST API. Untuk menguji atau mencoba API langsung (tanpa melalui frontend Laravel), Anda bisa menggunakan *tools* seperti **Postman** atau **Thunder Client** (ekstensi VS Code).

### Metode HTTP yang Digunakan
- `GET`: Untuk mengambil atau membaca data (contoh: daftar produk, laporan).
- `POST`: Untuk membuat atau menambahkan data baru (contoh: login, tambah kategori).
- `PUT`: Untuk memperbarui data yang sudah ada secara keseluruhan (contoh: edit produk).
- `DELETE`: Untuk menghapus data (contoh: hapus merek).

### Autentikasi (JWT)
Hampir seluruh rute (selain login/register dan get katalog publik) dilindungi oleh JWT (JSON Web Token).
Jika Anda mendapat respon `401 Unauthorized`, ikuti langkah berikut:
1. Lakukan permintaan `POST` ke `/api/auth/login` untuk mendapatkan `token`.
2. Pada *tab* **Headers** di Postman/Thunder Client, tambahkan:
   - Key: `Authorization`
   - Value: `Bearer <token_yang_didapat>`

### Contoh Permintaan (Requests)

#### 1. Login (Mendapatkan Token)
- **Method**: `POST`
- **URL**: `http://localhost:5000/api/auth/login`
- **Body** (JSON):
  ```json
  {
    "username": "admin",
    "password": "password123"
  }
  ```

#### 2. Menambahkan Kategori Baru
- **Method**: `POST`
- **URL**: `http://localhost:5000/api/categories`
- **Headers**: `Authorization: Bearer <token>`
- **Body** (JSON):
  ```json
  {
    "name": "Sneakers"
  }
  ```

#### 3. Mengambil Semua Katalog Produk
- **Method**: `GET`
- **URL**: `http://localhost:5000/api/catalog`
- *(Bisa diakses tanpa token pada konfigurasi saat ini untuk etalase publik)*

#### 4. Mencatat Transaksi Keluar (Penjualan)
- **Method**: `POST`
- **URL**: `http://localhost:5000/api/transactions`
- **Headers**: `Authorization: Bearer <token>`
- **Body** (JSON):
  ```json
  {
    "product_variant_id": 1,
    "type": "OUT",
    "quantity": 2,
    "reason": "Terjual via toko offline"
  }
  ```
