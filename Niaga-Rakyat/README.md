🧾 Niaga Rakyat POS System v0.1

Sistem Kasir (Point of Sale) berbasis web yang modern, responsif, dan mudah digunakan.
Dibangun menggunakan PHP Native, Tailwind CSS, dan MySQL.








📌 Changelog
🚀 v0.1 (Maret 2026)

Rilis perdana aplikasi

Fitur dasar manajemen produk, kategori, supplier

Modul transaksi kasir

Laporan penjualan

Sistem login multi-level

🔮 Rencana Update Selanjutnya
Versi	Fitur
v0.2	Grafik lebih interaktif & dashboard kustom
v0.3	Modul pembelian & stok masuk
v0.4	Manajemen pelanggan & hutang piutang
v0.5	Notifikasi stok menipis
v0.6	Backup database otomatis
v0.7	Multi-cabang
v0.8	Integrasi barcode scanner
v0.9	Aplikasi mobile companion
v1.0	Rilis stabil semua fitur utama
✨ Fitur Utama

📊 Dashboard interaktif (statistik real-time)

📦 Manajemen produk (CRUD + upload gambar)

🏷️ Manajemen kategori & supplier

🛒 Transaksi kasir (POS cepat & intuitif)

🧾 Cetak struk 80mm

📈 Laporan penjualan (filter tanggal + export CSV/Excel)

👥 Multi-level user (Admin & Kasir)

📱 Responsive design (Desktop, Tablet, Mobile)

🛠️ Teknologi yang Digunakan
Backend

PHP Native (tanpa framework)

Database

MySQL

Frontend

Tailwind CSS (CDN)

Font Awesome 6

DataTables

Chart.js

Library Tambahan

jQuery

PhpSpreadsheet (Export Excel)

💻 Persyaratan Sistem

PHP 7.4+

MySQL 5.7+

Apache / Nginx

Browser modern (Chrome, Firefox, Edge, Safari)

⚙️ Instalasi
1️⃣ Clone Repository
git clone https://github.com/username/niaga-rakyat-pos.git
cd niaga-rakyat-pos
2️⃣ Buat Database

Buka phpMyAdmin atau terminal MySQL

Buat database baru:

CREATE DATABASE kasir_pos;

Import file database.sql

3️⃣ Konfigurasi Database

Buka file config.php, lalu sesuaikan:

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kasir_pos');
4️⃣ Buat Folder Uploads
mkdir uploads
mkdir uploads/products

Pastikan folder memiliki permission write (755 atau 777).

5️⃣ Install Dependency (Opsional - Export Excel)
composer require phpoffice/phpspreadsheet

Jika tidak menggunakan fitur export Excel, langkah ini bisa dilewati.

6️⃣ Akses Aplikasi

Buka browser:

http://localhost/kasir/
🔑 Login Default

Admin

Username: admin

Password: admin123

Kasir

Username: kasir

Password: kasir123

📁 Struktur Folder
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
📖 Cara Penggunaan
📦 Manajemen Produk

Buka menu Produk

Klik Tambah Produk

Isi form (barcode, nama, kategori, supplier, harga, stok)

Klik Simpan

🛒 Transaksi Kasir

Buka menu Kasir

Cari produk

Klik produk untuk menambahkan ke keranjang

Atur jumlah

Masukkan nominal bayar

Klik Proses Transaksi

Cetak struk jika diperlukan

📊 Laporan Penjualan

Buka menu Laporan

Pilih rentang tanggal

Klik Tampilkan

Export ke CSV / Excel jika diperlukan

🤝 Kontribusi

Kami sangat terbuka untuk kontribusi!

Fork repository

Buat branch baru:

git checkout -b fitur-baru

Commit perubahan:

git commit -m "Menambahkan fitur baru"

Push:

git push origin fitur-baru

Buat Pull Request

📜 Lisensi

Hak cipta dilindungi undang-undang.
© 2026 Julyant Marco Melandry
All Rights Reserved.

📞 Kontak

Julyant Marco Melandry

Email: email@example.com

GitHub: https://github.com/username

Website: https://example.com

❤️ Credits

Dibuat dengan ❤️ oleh Julyant Marco Melandry
