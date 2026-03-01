# Niaga Rakyat POS System v0.1

Sistem Kasir (Point of Sale) berbasis web yang modern, responsif, dan mudah digunakan. Dibangun dengan PHP Native, Tailwind CSS, dan MySQL.

![Version](https://img.shields.io/badge/version-0.1-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)

---

## Changelog

### v0.1 (Maret 2026)
- Rilis perdana aplikasi
- Fitur dasar manajemen produk, kategori, supplier
- Modul transaksi kasir
- Laporan penjualan
- Sistem login multi-level

---

## Fitur Utama

- **Dashboard Interaktif**: Statistik real-time dengan grafik penjualan
- **Manajemen Produk**: CRUD produk dengan kategori, supplier, upload gambar
- **Manajemen Kategori & Supplier**: Kelola data master dengan mudah
- **Transaksi Kasir (POS)**: Antarmuka kasir cepat, mudah
- **Cetak Struk**: Format struk 80mm siap cetak
- **Laporan Penjualan**: Filter tanggal, export CSV/Excel
- **Multi-level User**: Role Admin & Kasir
- **Responsive Design**: Desktop, tablet, mobile

---

## Teknologi yang Digunakan

- **Backend**: PHP Native (tanpa framework)
- **Database**: MySQL
- **Frontend**:
  - Tailwind CSS (CDN)
  - Font Awesome 6
  - DataTables
  - Chart.js
- **Library Tambahan**:
  - jQuery
  - PhpSpreadsheet (export Excel)

---

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web Server (Apache/Nginx)
- Browser modern (Chrome, Firefox, Edge, Safari)

---

## Instalasi

1. **Clone Repository**
    ```bash
    git clone https://github.com/username/niaga-rakyat-pos.git
    cd niaga-rakyat-pos
    ```

2. **Buat Database**
    - Buka phpMyAdmin / terminal MySQL
    - Jalankan/Import file `database.sql` yang sudah disediakan

3. **Konfigurasi Database**
    - Buka file `config.php`
    - Ubah jika perlu:
      ```php
      define('DB_HOST', 'localhost');
      define('DB_USER', 'root');
      define('DB_PASS', '');
      define('DB_NAME', 'kasir_pos');
      ```

4. **Buat Folder Uploads**
    ```bash
    mkdir uploads
    mkdir uploads/products
    chmod 777 uploads/products
    ```

5. **Install Dependencies** (Opsional untuk export Excel)
    ```bash
    composer require phpoffice/phpspreadsheet
    ```

6. **Akses Aplikasi**
    - Buka browser: [http://localhost/kasir/](http://localhost/kasir/)
    - Login default:
      - **Admin**: `admin` / `admin123`
      - **Kasir**: `kasir` / `kasir123`

---

## Struktur Folder

```
niaga-rakyat-pos/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── logo/
│       ├── logo.png
│       ├── logoicon.png
│       └── logosidebar.png
├── uploads/
│   └── products/
├── api/
│   └── get_product.php
├── config.php
├── database.sql
├── dashboard.php
├── index.php
├── pos.php
├── products.php
├── receipt.php
├── reports.php
├── sidebar.php
├── transactions.php
├── export.php
└── README.md
```

---

## Cara Penggunaan

### Manajemen Produk
- Buka menu **Produk**
- Klik **Tambah Produk**
- Isi form (barcode, nama, kategori, supplier, harga, stok)
- Klik **Simpan**

### Transaksi Kasir
- Buka menu **Kasir**
- Cari produk di pencarian
- Klik produk yang ingin dibeli
- Atur jumlah di keranjang
- Masukkan uang bayar
- Klik **Proses Transaksi**
- Cetak struk jika perlu

### Laporan Penjualan
- Buka menu **Laporan**
- Pilih rentang tanggal
- Klik **Tampilkan**
- Export ke CSV/Excel jika diinginkan

---

## Kontribusi

Kontribusi sangat terbuka!  
Fork repository ini dan buat pull request untuk penambahan fitur atau perbaikan bug.

Langkah:

1. Fork repository
2. Buat branch baru:
    ```bash
    git checkout -b fitur-baru
    ```
3. Commit perubahan:
    ```bash
    git commit -m 'Menambahkan fitur baru'
    ```
4. Push ke branch:
    ```bash
    git push origin fitur-baru
    ```
5. Buat Pull Request

---

## Lisensi

MIT License  
© 2026 Julyant Marco Melandry  
All Rights Reserved.

---

## Kontak

**Julyant Marco Melandry**  
Email: email@example.com  
GitHub: [github.com/username](https://github.com/username)  
Website: [example.com](https://example.com)

---

## Credits

Dibuat dengan ❤️ oleh Julyant Marco Melandry

---