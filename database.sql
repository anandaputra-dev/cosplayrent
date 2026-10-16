CREATE DATABASE IF NOT EXISTS rental_kostum CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rental_kostum;

CREATE TABLE IF NOT EXISTS pengguna (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_lengkap VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  kata_sandi VARCHAR(255) NOT NULL,
  peran VARCHAR(20) NOT NULL DEFAULT 'pengguna'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kategori (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kostum (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kategori_id INT UNSIGNED NULL,
  nama_kostum VARCHAR(150) NOT NULL,
  nama_karakter VARCHAR(150) NULL,
  nama_serial VARCHAR(150) NULL,
  ukuran VARCHAR(50) NULL,
  deskripsi TEXT NULL,
  harga_sewa_per_hari DECIMAL(12,2) NOT NULL DEFAULT 0,
  status VARCHAR(50) NOT NULL DEFAULT 'tersedia',
  CONSTRAINT fk_kostum_kategori FOREIGN KEY (kategori_id) REFERENCES kategori(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Jika database rental_kostum sudah berisi tabel dengan struktur sendiri,
-- jangan jalankan file ini; cukup pastikan config/database.php mengarah ke database tersebut.
