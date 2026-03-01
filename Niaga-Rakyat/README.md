# 🧾 Niaga Rakyat POS System

Sistem Kasir (Point of Sale) berbasis web yang modern, responsif, dan mudah digunakan.  
Dibangun menggunakan **PHP Native**, **Tailwind CSS**, dan **MySQL**.

![Version](https://img.shields.io/badge/version-0.1-blue)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![License](https://img.shields.io/badge/license-MIT-green)

---

## 🚀 Versi

### v0.1 — Maret 2026
- Rilis perdana aplikasi
- Manajemen produk, kategori, supplier
- Modul transaksi kasir
- Laporan penjualan
- Sistem login multi-level (Admin & Kasir)

---

## 🔮 Roadmap Pengembangan

- v0.2 — Grafik interaktif & dashboard kustom
- v0.3 — Modul pembelian & stok masuk
- v0.4 — Manajemen pelanggan & hutang piutang
- v0.5 — Notifikasi stok menipis
- v0.6 — Backup database otomatis
- v0.7 — Multi-cabang
- v0.8 — Integrasi barcode scanner
- v0.9 — Aplikasi mobile companion
- v1.0 — Rilis stabil semua fitur utama

---

## ✨ Fitur Utama

- 📊 Dashboard statistik real-time
- 📦 CRUD produk + upload gambar
- 🏷️ Manajemen kategori & supplier
- 🛒 Transaksi kasir cepat & intuitif
- 🧾 Cetak struk 80mm
- 📈 Laporan penjualan (filter tanggal)
- 📤 Export CSV & Excel
- 👥 Multi-level user (Admin & Kasir)
- 📱 Responsive (Desktop, Tablet, Mobile)

---

## 🛠️ Teknologi

**Backend**
- PHP Native (tanpa framework)

**Frontend**
- Tailwind CSS (CDN)
- Font Awesome 6
- DataTables
- Chart.js
- jQuery

**Database**
- MySQL

**Library Tambahan**
- PhpSpreadsheet (Export Excel)

---

## 💻 Persyaratan Sistem

- PHP 7.4+
- MySQL 5.7+
- Apache / Nginx
- Browser modern

---

## ⚙️ Instalasi

### 1️⃣ Clone Repository

```bash
git clone https://github.com/username/niaga-rakyat-pos.git
cd niaga-rakyat-pos
```

---

### 2️⃣ Buat Database

Buat database baru:

```sql
CREATE DATABASE kasir_pos;
```

Import file:
```
database.sql
```

---

### 3️⃣ Konfigurasi Database

Edit file `config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kasir_pos');
```

---

### 4️⃣ Buat Folder Upload

```bash
mkdir uploads
mkdir uploads/products
```

Pastikan permission write (755 / 777).

---

### 5️⃣ Install Dependency (Opsional)

Untuk fitur export Excel:

```bash
composer require phpoffice/phpspreadsheet
```

---

### 6️⃣ Jalankan Aplikasi

Buka di browser:

```
http://localhost/kasir/
```

---

## 🔑 Login Default

**Admin**
```
Username: admin
Password: admin123
```

**Kasir**
```
Username: kasir
Password: kasir123
```

---

## 📁 Struktur Folder

```
niaga-rakyat-pos/
│
├── assets/
│   ├── css/style.css
│   ├── js/script.js
│   └── logo/
│
├── uploads/products/
├── api/get_product.php
├── config.php
├── database.sql
├── dashboard.php
├── index.php
├── pos.php
├── products.php
├── receipt.php
├── reports.php
├── transactions.php
├── export.php
└── README.md
```

---

## 📖 Cara Penggunaan

### 📦 Tambah Produk
1. Masuk menu **Produk**
2. Klik **Tambah Produk**
3. Isi data
4. Klik **Simpan**

### 🛒 Transaksi Kasir
1. Masuk menu **Kasir**
2. Cari produk
3. Tambahkan ke keranjang
4. Masukkan nominal bayar
5. Klik **Proses Transaksi**
6. Cetak struk

### 📊 Laporan
1. Masuk menu **Laporan**
2. Pilih tanggal
3. Klik **Tampilkan**
4. Export jika diperlukan

---

## 🤝 Kontribusi

1. Fork repository
2. Buat branch baru
   ```bash
   git checkout -b fitur-baru
   ```
3. Commit
   ```bash
   git commit -m "Menambahkan fitur baru"
   ```
4. Push & Pull Request

---

## 📜 Lisensi

© 2026 Julyant Marco Melandry  
All Rights Reserved.

---

## 📞 Kontak

Julyant Marco Melandry  
Email: email@example.com  
GitHub: https://github.com/username  
Website: https://example.com  

---

## ❤️ Credits

Dibuat dengan ❤️ oleh Julyant Marco Melandry
