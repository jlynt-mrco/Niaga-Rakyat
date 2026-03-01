-- database.sql - Struktur database dan data awal
CREATE DATABASE IF NOT EXISTS kasir_pos;
USE kasir_pos;

-- ===== TABEL USERS =====
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'kasir') DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== TABEL CATEGORIES =====
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== TABEL SUPPLIERS =====
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_supplier VARCHAR(100) NOT NULL,
    no_telp VARCHAR(20),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== TABEL PRODUCTS =====
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

-- ===== TABEL SALES =====
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

-- ===== TABEL SALE_DETAILS =====
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

-- ===== DATA AWAL =====
INSERT INTO users (username, password, nama_lengkap, role) VALUES
('admin', MD5('admin123'), 'Administrator', 'admin'),
('kasir', MD5('kasir123'), 'Kasir Toko', 'kasir');

INSERT INTO categories (nama_kategori) VALUES
('Makanan'), ('Minuman'), ('Elektronik'), ('Pakaian'), ('Lainnya');

INSERT INTO suppliers (nama_supplier, no_telp, alamat) VALUES
('Supplier 1', '021-1234567', 'Jakarta'),
('Supplier 2', '031-7654321', 'Surabaya'),
('Supplier 3', '024-5555555', 'Semarang');