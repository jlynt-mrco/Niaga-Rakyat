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

## Fitur Utama

- **Dashboard Interaktif** - Statistik real-time dengan grafik penjualan
- **Manajemen Produk** - CRUD produk dengan kategori, supplier, dan upload gambar
- **Manajemen Kategori & Supplier** - Kelola data master dengan mudah
- **Transaksi Kasir (POS)** - Antarmuka kasir yang cepat dan intuitif
- **Cetak Struk** - Format struk 80mm siap cetak
- **Laporan Penjualan** - Filter tanggal, export CSV/Excel
- **Multi-level User** - Role Admin dan Kasir
- **Responsive Design** - Bisa diakses via desktop, tablet, dan mobile

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

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web Server (Apache/Nginx)
- Browser modern (Chrome, Firefox, Edge, Safari)

## Instalasi Lengkap

[Struktur Folder]
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

[1. Clone Repository]
```bash
git clone https://github.com/username/niaga-rakyat-pos.git
cd niaga-rakyat-pos

[2. Buat Database dan Import SQL]
sql
CREATE DATABASE IF NOT EXISTS kasir_pos;
USE kasir_pos;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'kasir') DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_supplier VARCHAR(100) NOT NULL,
    no_telp VARCHAR(20),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    barcode VARCHAR(50) UNIQUE,
    nama_produk VARCHAR(100) NOT NULL,
    kategori_id INT NULL,
    supplier_id INT NULL,
    harga_beli DECIMAL(10,2) NOT NULL,
    harga_jual DECIMAL(10,2) NOT NULL,
    stok INT DEFAULT 0,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

CREATE TABLE sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice VARCHAR(50) UNIQUE NOT NULL,
    user_id INT,
    total_harga DECIMAL(10,2) NOT NULL,
    diskon DECIMAL(10,2) DEFAULT 0,
    pajak DECIMAL(10,2) DEFAULT 0,
    total_bayar DECIMAL(10,2) NOT NULL,
    uang_bayar DECIMAL(10,2) NOT NULL,
    uang_kembali DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE sale_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT,
    product_id INT,
    harga_jual DECIMAL(10,2) NOT NULL,
    jumlah INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

INSERT INTO users (username, password, nama_lengkap, role) VALUES
('admin', MD5('admin123'), 'Administrator', 'admin'),
('kasir', MD5('kasir123'), 'Kasir Toko', 'kasir');

INSERT INTO categories (nama_kategori) VALUES
('Makanan'), ('Minuman'), ('Elektronik'), ('Pakaian'), ('Lainnya');

INSERT INTO suppliers (nama_supplier, no_telp, alamat) VALUES
('Supplier 1', '021-1234567', 'Jakarta'),
('Supplier 2', '031-7654321', 'Surabaya'),
('Supplier 3', '024-5555555', 'Semarang');

3. Konfigurasi Koneksi Database
Buka file config.php dan sesuaikan konfigurasi database jika perlu:

php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kasir_pos');
define('BASE_URL', 'http://localhost/kasir/');
define('APP_NAME', 'Niaga Rakyat');

[4. Buat Folder Uploads]
bash
mkdir uploads
mkdir uploads/products
chmod 777 uploads/products

[5. Install Dependencies (Opsional untuk export Excel)]
bash
composer require phpoffice/phpspreadsheet

[6. Akses Aplikasi]
Buka browser dan akses: http://localhost/kasir/

Login dengan akun default:

Admin: username admin, password admin123

Kasir: username kasir, password kasir123
