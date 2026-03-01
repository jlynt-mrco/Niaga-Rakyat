# Niaga Rakyat POS System v0.1

Sistem Kasir (Point of Sale) berbasis web yang modern, responsif, dan mudah digunakan. Dibangun dengan PHP Native, Tailwind CSS, dan MySQL.

![Version](https://img.shields.io/badge/version-0.1-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)

## Changelog

### v0.1 (Maret 2026)
- Rilis perdana aplikasi
- Fitur dasar manajemen produk, kategori, supplier
- Modul transaksi kasir
- Laporan penjualan
- Sistem login multi-level

### Rencana Update Selanjutnya
- v0.2 - Fitur grafik yang lebih interaktif, dashboard kustom
- v0.3 - Modul pembelian dan stok masuk
- v0.4 - Manajemen pelanggan dan hutang piutang
- v0.5 - Notifikasi stok menipis
- v0.6 - Backup database otomatis
- v0.7 - Multi-cabang
- v0.8 - Integrasi barcode scanner
- v0.9 - Aplikasi mobile companion
- v1.0 - Rilis stabil dengan semua fitur utama

## Fitur Utama

- Dashboard Interaktif - Statistik real-time dengan grafik penjualan
- Manajemen Produk - CRUD produk dengan kategori, supplier, dan upload gambar
- Manajemen Kategori & Supplier - Kelola data master dengan mudah
- Transaksi Kasir (POS) - Antarmuka kasir yang cepat dan intuitif
- Cetak Struk - Format struk 80mm siap cetak
- Laporan Penjualan - Filter tanggal, export CSV/Excel
- Multi-level User - Role Admin dan Kasir
- Responsive Design - Bisa diakses via desktop, tablet, dan mobile

## Teknologi yang Digunakan

- Backend: PHP Native (tanpa framework)
- Database: MySQL
- Frontend: 
  - Tailwind CSS (CDN)
  - Font Awesome 6
  - DataTables
  - Chart.js
- Library Tambahan:
  - jQuery
  - PhpSpreadsheet (export Excel)

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web Server (Apache/Nginx)
- Browser modern (Chrome, Firefox, Edge, Safari)

## Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/niaga-rakyat-pos.git
   cd niaga-rakyat-pos
Buat Database

Buka phpMyAdmin atau terminal MySQL

Buat database baru dengan nama kasir_pos

Import file database.sql yang sudah disediakan

Konfigurasi Koneksi Database

Buka file config.php

Sesuaikan konfigurasi database jika perlu:

php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kasir_pos');
Buat Folder Uploads

bash
mkdir uploads
mkdir uploads/products
Pastikan folder uploads/products memiliki permission write (755 atau 777)

Install Dependencies (Opsional untuk export Excel)

bash
composer require phpoffice/phpspreadsheet
Jika tidak menggunakan fitur export Excel, bisa diabaikan

Akses Aplikasi

Buka browser dan akses: http://localhost/kasir/

Login dengan akun default:

Admin: username: admin, password: admin123

Kasir: username: kasir, password: kasir123

Struktur Folder
text
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
Cara Penggunaan
Manajemen Produk
Buka menu Produk

Klik tombol Tambah Produk untuk menambah produk baru

Isi form produk (barcode, nama, kategori, supplier, harga, stok)

Klik Simpan

Transaksi Kasir
Buka menu Kasir

Cari produk menggunakan kotak pencarian

Klik produk yang ingin dibeli

Atur jumlah di keranjang

Masukkan nominal uang bayar

Klik Proses Transaksi

Cetak struk jika diperlukan

Laporan Penjualan
Buka menu Laporan

Pilih rentang tanggal

Klik Tampilkan

Export ke CSV atau Excel jika diperlukan

Kontribusi
Kami sangat terbuka untuk kontribusi! Silakan fork repository ini dan buat pull request untuk penambahan fitur atau perbaikan bug.

Fork repository

Buat branch baru (git checkout -b fitur-baru)

Commit perubahan (git commit -m 'Menambahkan fitur baru')

Push ke branch (git push origin fitur-baru)

Buat Pull Request

Lisensi
Hak cipta dilindungi undang-undang. © 2026 Julyant Marco Melandry.
All Rights Reserved.

Kontak
Julyant Marco Melandry

Email: email@example.com

GitHub: github.com/username

Website: example.com

Credits
Dibuat dengan ❤️ oleh Julyant Marco Melandry
