# Cara Kerja Aplikasi Manajemen Katalog Sepatu

Dokumen ini menjelaskan arsitektur, aliran data, dan struktur basis data dari Aplikasi Manajemen Katalog Sepatu melalui diagram visual. Semua diagram di bawah ini dirender menggunakan **Mermaid.js**.

---

## 1. Arsitektur Sistem (System Architecture)

Aplikasi ini menggunakan pola arsitektur **Client-Server** dengan pemisahan penuh antara Frontend (Antarmuka Pengguna) dan Backend (API & Database).

```mermaid
graph LR
    subgraph Frontend [Frontend - Laravel UI]
        Browser((Web Browser))
        UI[Views & Blade Templates]
        JS[Client-Side JS & Fetch API]
    end

    subgraph Backend [Backend - Express.js API]
        Router[Express Routes]
        Controller[Controllers]
        ORM[Sequelize ORM]
    end

    subgraph Database [Database MySQL]
        DB[(katalog_sepatu)]
    end

    Browser <-->|HTTP / Render HTML| UI
    Browser <-->|AJAX Requests JSON| JS
    JS <-->|REST API via HTTP/JSON| Router
    Router --> Controller
    Controller <--> ORM
    ORM <-->|SQL Queries| DB

    classDef fEnd fill:#f9f9f9,stroke:#333,stroke-width:2px;
    classDef bEnd fill:#e1f5fe,stroke:#0288d1,stroke-width:2px;
    classDef db fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px;
    
    class Frontend fEnd;
    class Backend bEnd;
    class Database db;
```

**Penjelasan:**
1. **Frontend** (Berjalan di Port 8000): Laravel hanya bertugas melayani kerangka HTML (Blade). Semua data dinamis diambil oleh JavaScript dari browser (Fetch API).
2. **Backend** (Berjalan di Port 5000): Bertindak sebagai penyedia REST API. Menerima permintaan, memvalidasi _token_ (jika ada), menjalankan logika bisnis, dan berkomunikasi dengan database.
3. **Database**: MySQL menyimpan seluruh state aplikasi yang dijembatani oleh Sequelize ORM di backend.

---

## 2. Entity Relationship Diagram (ERD)

Berikut adalah struktur skema database rasional yang menggerakkan sistem ini. Terdapat relasi kompleks untuk menangani variasi stok sepatu berdasarkan ukuran.

```mermaid
erDiagram
    USER {
        int id PK
        string username
        string password
    }
    
    CATEGORY {
        int id PK
        string name
    }
    
    BRAND {
        int id PK
        string name
    }

    SIZE {
        int id PK
        int size_value
    }
    
    PRODUCT {
        int id PK
        string name
        string description
        decimal price
        string imageUrl
        int category_id FK
        int brand_id FK
    }

    PRODUCT_VARIANT {
        int id PK
        int product_id FK
        int size_id FK
        int stock
    }

    STOCK_TRANSACTION {
        int id PK
        int product_variant_id FK
        enum type "IN/OUT"
        int quantity
        string reason
        datetime date
    }

    CATEGORY ||--o{ PRODUCT : "has many"
    BRAND ||--o{ PRODUCT : "has many"
    PRODUCT ||--o{ PRODUCT_VARIANT : "has variants"
    SIZE ||--o{ PRODUCT_VARIANT : "used in"
    PRODUCT_VARIANT ||--o{ STOCK_TRANSACTION : "logged in"
```

**Penjelasan Relasi Utama:**
- Satu **Product** bisa memiliki banyak **Product_Variant** (contoh: Sepatu A ukuran 40, Sepatu A ukuran 41).
- Stok sebenarnya tidak disimpan di tabel `PRODUCT`, melainkan di tabel `PRODUCT_VARIANT`.
- Setiap kali jumlah stok varian berubah, sebuah rekaman masuk ke dalam **Stock_Transaction** (baik itu `IN` / barang masuk, maupun `OUT` / barang keluar).

---

## 3. Alur Permintaan (Sequence Diagram) - Contoh: Proses Tambah Transaksi Penjualan

Diagram urutan ini mengilustrasikan apa yang terjadi ketika admin menginput penjualan / barang keluar (`OUT`).

```mermaid
sequenceDiagram
    actor Admin
    participant Frontend as Frontend (JS)
    participant Auth as Auth Middleware
    participant Controller as Transaction Controller
    participant DB as MySQL DB

    Admin->>Frontend: Mengisi form transaksi stok (Tipe: OUT, Qty: 2)
    Frontend->>Auth: POST /api/transactions (Headers: Bearer Token)
    
    alt Token Tidak Valid / Kadaluarsa
        Auth-->>Frontend: 401 Unauthorized
        Frontend-->>Admin: Peringatan Sesi Berakhir -> Redirect Login
    else Token Valid
        Auth->>Controller: Lanjutkan request
        
        Controller->>DB: Cari ProductVariant (Lock for update)
        DB-->>Controller: Return Variant (Stock saat ini: 5)
        
        alt Stok Tidak Cukup (Qty > Stock)
            Controller-->>Frontend: 400 Bad Request (Stok tidak mencukupi)
            Frontend-->>Admin: Tampilkan pesan error
        else Stok Cukup
            Controller->>DB: Update stok Variant (5 - 2 = 3)
            Controller->>DB: Simpan log ke StockTransaction
            DB-->>Controller: Commit sukses
            Controller-->>Frontend: 201 Created (Transaksi berhasil)
            Frontend-->>Admin: Tampilkan SweetAlert sukses & Update UI
        end
    end
```

**Penjelasan:**
Proses ini diamankan oleh _Database Transaction_. Jika saat menyimpan log transaksi gagal, maka pengurangan stok juga akan dibatalkan (*rollback*), sehingga memastikan data selalu akurat dan tidak _corrupt_.
