-- Buat database
CREATE DATABASE IF NOT EXISTS ukm_desa1;
USE ukm_desa1;

-- Drop tabel yang ada (dalam urutan yang benar untuk menghindari foreign key constraint)
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS ukm;
DROP TABLE IF EXISTS pemilik_ukm;
DROP TABLE IF EXISTS kategori_ukm;
DROP TABLE IF EXISTS kategori_produk;
DROP TABLE IF EXISTS users;

-- Drop view yang ada
DROP VIEW IF EXISTS v_ukm_produk;
DROP VIEW IF EXISTS v_detail_produk;

-- Buat tabel kategori_ukm
CREATE TABLE kategori_ukm (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL,
    deskripsi TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buat tabel pemilik_ukm
CREATE TABLE pemilik_ukm (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buat tabel kategori_produk
CREATE TABLE kategori_produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL,
    deskripsi TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buat tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user
INSERT INTO users (username, password, name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@example.com', 'admin'),
('biimaaa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bima Adji', 'bima@example.com', 'user');

-- Insert data kategori UKM awal
INSERT INTO kategori_ukm (nama_kategori, deskripsi) VALUES
('Makanan', 'Produk makanan dan minuman'),
('Kerajinan', 'Produk kerajinan tangan'),
('Pertanian', 'Produk hasil pertanian'),
('Peternakan', 'Produk hasil peternakan'),
('Fashion', 'Produk pakaian dan aksesoris'),
('Lainnya', 'Kategori produk lainnya');

-- Insert data kategori produk awal
INSERT INTO kategori_produk (nama_kategori, deskripsi) VALUES
('Makanan', 'Produk makanan dan minuman'),
('Kerajinan', 'Produk kerajinan tangan'),
('Pertanian', 'Produk hasil pertanian'),
('Peternakan', 'Produk hasil peternakan'),
('Fashion', 'Produk pakaian dan aksesoris'),
('Lainnya', 'Kategori produk lainnya');

-- Insert data pemilik UKM
INSERT INTO pemilik_ukm (nama, telepon, email, alamat) VALUES
('Didi Supriadi', '082345678901', 'didi@email.com', 'Jl. Raya Desa No. 10, Desa Sukamaju'),
('Siti Asmanah', '087812345678', 'siti@email.com', 'Jl. Bambu No. 5, Desa Sukamaju'),
('Hadi Santoso', '089567891234', 'hadi@email.com', 'Jl. Kebun Raya No. 15, Desa Sukamaju'),
('Asep Kuswanto', '081224516162', 'asep@email.com', 'Jl. Kebun Raya No. 42, Desa Sukamaju'),
('Titiek Susanti', '081224524516', 'titiek@email.com', 'Jl. Raya Desa No. 1, Desa Sukamaju'),
('Antoni Martial', '081224516834', 'antoni@email.com', 'Jl. Raya Old Trafford No. 5, Desa Sukamaju');

-- Buat tabel ukm
DROP TABLE IF EXISTS ukm;
CREATE TABLE ukm (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_ukm VARCHAR(100) NOT NULL,
    pemilik_id INT NOT NULL,
    alamat TEXT NOT NULL,
    kategori_id INT NOT NULL,
    deskripsi TEXT NOT NULL,
    jumlah_karyawan INT DEFAULT 0,
    tahun_berdiri YEAR,
    status ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif',
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pemilik_id) REFERENCES pemilik_ukm(id),
    FOREIGN KEY (kategori_id) REFERENCES kategori_ukm(id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Verify table structure
SHOW CREATE TABLE ukm;

-- Insert data UKM
INSERT INTO ukm (nama_ukm, pemilik_id, alamat, kategori_id, deskripsi, jumlah_karyawan, tahun_berdiri, user_id) VALUES
('Bakso Pak Didi', 1, 'Jl. Raya Desa No. 10, Desa Sukamaju', 1, 'Bakso dengan daging sapi pilihan, berdiri sejak 2010 dan sudah memiliki cabang di beberapa desa tetangga.', 5, 2010, 1),
('Kerajinan Bamboo', 2, 'Jl. Bambu No. 5, Desa Sukamaju', 2, 'Memproduksi berbagai kerajinan dari bambu, seperti tempat tisu, lampu hias, dan perabotan rumah tangga.', 3, 2015, 1),
('Tani Makmur', 3, 'Jl. Kebun Raya No. 15, Desa Sukamaju', 3, 'Produksi sayuran organik dengan sistem hidroponik.', 7, 2018, 3),
('Batik Sangar', 4, 'Jl. Kebun Raya No. 42, Desa Sukamaju', 2, 'Kerajinan batik sangar dengan kualitas yang bagus', 3, 2010, 3),
('Yuk Jajan', 5, 'Jl. Raya Desa No. 1, Desa Sukamaju', 1, 'Produksi jajan enak dan murah.', 5, 2012, 3),
('Susu Sapi Pak Anto', 6, 'Jl. Raya Old Trafford No. 5, Desa Sukamaju', 4, 'Susu Sapi yang berkualitas dan sehat.', 6, 2007, 1);

-- Buat tabel produk
CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id INT NOT NULL,
    kategori_id INT NOT NULL,
    nama_produk VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10, 2) NOT NULL,
    stok INT DEFAULT 0,
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE,
    FOREIGN KEY (kategori_id) REFERENCES kategori_produk(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data produk
INSERT INTO produk (ukm_id, kategori_id, nama_produk, deskripsi, harga, stok) VALUES
(1, 1, 'Bakso Jumbo', 'Bakso berukuran besar dengan isian daging', 15000, 50),
(1, 1, 'Bakso Urat', 'Bakso dengan campuran urat sapi', 12000, 60),
(1, 1, 'Bakso Puyuh', 'Bakso dengan isian telur puyuh', 14000, 60),
(2, 2, 'Keranjang Bambu', 'Keranjang multifungsi dari bambu pilihan', 75000, 20),
(2, 2, 'Lampu Hias Bambu', 'Lampu hias dari bambu dengan desain elegan', 120000, 15),
(3, 3, 'Sayur Selada', 'Selada segar hidroponik per ikat', 8000, 100),
(3, 3, 'Sayur Kangkung', 'Kangkung segar organik per ikat', 5000, 150),
(3, 3, 'Bayam', 'Bayam segar organik per ikat', 4000, 120),
(3, 3, 'Daun Bawang', 'Daun Bawang segar organik per ikat', 2000, 130),
(4, 2, 'Kain Batik', 'Kain Batik didesain dengan tangan sendiri', 1000000, 15),
(4, 2, 'Sarung Batik', 'Sarung Batik dengan kualitas bagus', 500000, 20),
(5, 1, 'Nastar', 'Nastar satu toples', 15000, 100),
(5, 1, 'Kastangel', 'Kastangel satu toples', 14000, 79),
(5, 1, 'Putri Salju', 'Putri Salju satu toples', 20000, 30),
(5, 1, 'Biskuit', 'Biskuit satu toples', 10000, 50),
(5, 1, 'Keripik', 'Keripik per 1 ons', 4000, 100),
(6, 4, 'Susu Sapi Original', 'Susu 500 ml per botol', 4000, 100),
(6, 4, 'Susu Sapi Coklat', 'Susu 500 ml per botol', 5000, 80),
(6, 4, 'Susu Sapi Stroberi', 'Susu 500 ml per botol', 5000, 60);

-- Buat view untuk menampilkan UKM dengan jumlah produk
CREATE OR REPLACE VIEW v_ukm_produk AS
SELECT 
    u.id,
    u.nama_ukm,
    u.alamat,
    u.deskripsi,
    u.created_at,
    p.nama as nama_pemilik,
    p.telepon as telepon_pemilik,
    k.nama_kategori as kategori,
    COUNT(pr.id) as jumlah_produk
FROM ukm u
LEFT JOIN pemilik_ukm p ON u.pemilik_id = p.id
LEFT JOIN kategori_ukm k ON u.kategori_id = k.id
LEFT JOIN produk pr ON u.id = pr.ukm_id
GROUP BY u.id, u.nama_ukm, u.alamat, u.deskripsi, u.created_at, p.nama, p.telepon, k.nama_kategori;

-- Buat view untuk menampilkan detail produk
CREATE OR REPLACE VIEW v_detail_produk AS
SELECT 
    p.id,
    p.nama_produk,
    p.deskripsi,
    p.harga,
    p.stok,
    p.foto,
    u.nama_ukm,
    k.nama_kategori,
    pem.nama as nama_pemilik,
    pem.telepon as telepon_pemilik
FROM produk p
JOIN ukm u ON p.ukm_id = u.id
JOIN kategori_produk k ON p.kategori_id = k.id
JOIN pemilik_ukm pem ON u.pemilik_id = pem.id;

DESCRIBE ukm;

SHOW CREATE TABLE ukm;

SELECT DATABASE(); 