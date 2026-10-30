CREATE DATABASE IF NOT EXISTS rental_kostum
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rental_kostum;

CREATE TABLE IF NOT EXISTS pengguna (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_lengkap VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  kata_sandi VARCHAR(255) NOT NULL,
  peran ENUM('pengguna','admin') NOT NULL DEFAULT 'pengguna',
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kategori (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kostum (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_kostum VARCHAR(120) NOT NULL,
  kategori_id INT UNSIGNED NULL,
  kategori_teks VARCHAR(60) NULL,
  deskripsi TEXT NULL,
  harga_per_hari INT UNSIGNED NOT NULL DEFAULT 0,
  stok INT UNSIGNED NOT NULL DEFAULT 1,
  foto VARCHAR(500) NULL,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_kostum_kategori FOREIGN KEY (kategori_id) REFERENCES kategori(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO kategori (nama) VALUES
('Anime'), ('Game'), ('Superhero'), ('Fantasy'), ('Mitologi');

INSERT INTO kostum (nama_kostum, kategori_id, kategori_teks, deskripsi, harga_per_hari, stok, foto)
SELECT 'Naruto Uzumaki', id, 'Anime', 'Inspirasi karakter Naruto Shippuden.', 85000, 2,
'https://images.unsplash.com/photo-1601850494422-3cf14624b0b3?auto=format&fit=crop&w=700&q=80'
FROM kategori WHERE nama='Anime'
AND NOT EXISTS (SELECT 1 FROM kostum WHERE nama_kostum='Naruto Uzumaki');
INSERT INTO kostum (nama_kostum, kategori_id, kategori_teks, deskripsi, harga_per_hari, stok, foto)
SELECT 'Nezuko Kamado', id, 'Anime', 'Inspirasi karakter Demon Slayer.', 95000, 1,
'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=700&q=80'
FROM kategori WHERE nama='Anime'
AND NOT EXISTS (SELECT 1 FROM kostum WHERE nama_kostum='Nezuko Kamado');
INSERT INTO kostum (nama_kostum, kategori_id, kategori_teks, deskripsi, harga_per_hari, stok, foto)
SELECT 'Tanjiro Kamado', id, 'Anime', 'Inspirasi karakter Demon Slayer.', 90000, 1,
'https://images.unsplash.com/photo-1560972550-aba3456b5564?auto=format&fit=crop&w=700&q=80'
FROM kategori WHERE nama='Anime'
AND NOT EXISTS (SELECT 1 FROM kostum WHERE nama_kostum='Tanjiro Kamado');
INSERT INTO kostum (nama_kostum, kategori_id, kategori_teks, deskripsi, harga_per_hari, stok, foto)
SELECT 'Sailor Moon', id, 'Fantasy', 'Kostum bertema magical fantasy.', 110000, 1,
'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=700&q=80'
FROM kategori WHERE nama='Fantasy'
AND NOT EXISTS (SELECT 1 FROM kostum WHERE nama_kostum='Sailor Moon');
