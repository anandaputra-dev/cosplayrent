-- Jalankan setelah memilih database rental_kostum di phpMyAdmin.
-- Menambah kolom foto dan kota tanpa menghapus data kostum yang sudah ada.
ALTER TABLE kostum ADD COLUMN IF NOT EXISTS foto VARCHAR(500) NULL;
ALTER TABLE kostum ADD COLUMN IF NOT EXISTS kota VARCHAR(100) NULL;

-- Isi foto placeholder eksternal hanya jika belum ada foto; ganti dengan foto inventaris asli bila tersedia.
-- Kota sengaja tidak diisi otomatis agar lokasi kostum tidak menjadi data palsu.
-- Isi kolom kota sesuai lokasi kostum yang benar-benar tersedia.

-- Contoh pengisian foto per kostum: ubah ID dan URL sesuai foto yang kamu miliki.
-- UPDATE kostum SET foto = 'uploads/naruto.jpg', kota = 'Malang' WHERE id = 1;
