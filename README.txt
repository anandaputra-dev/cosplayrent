COSPLAYRENT - PHP Native + MySQL
================================

Fitur
- index.php       : landing page dengan katalog dari database
- register.php    : pendaftaran pengguna
- login.php       : login dengan password_hash/password_verify dan session
- dashboard.php   : dashboard pengguna
- gallery.php     : galeri kostum dari database
- detail.php      : detail kostum
- logout.php      : keluar akun
- config/database.php : konfigurasi koneksi PDO
- assets/         : CSS, JavaScript, dan gambar placeholder

CARA MENJALANKAN (XAMPP Windows)
1. Ekstrak folder kostumcosplay ke C:\xampp\htdocs\kostumcosplay.
2. Buka XAMPP Control Panel, jalankan Apache dan MySQL.
3. Buka config/database.php. Pastikan nama database, username, dan password sesuai XAMPP.
4. Database yang dipakai adalah rental_kostum. Karena proyek ini disesuaikan dengan tabel yang sudah ada,
   jangan impor database.sql jika database rental_kostum milikmu sudah berisi tabel kostum/kategori.
5. Buka http://localhost/kostumcosplay/

Struktur kolom yang dipakai
- pengguna: id, nama_lengkap, email, kata_sandi, peran
- kategori: id, nama_kategori
- kostum: id, kategori_id, nama_kostum, nama_karakter, nama_serial, ukuran,
  deskripsi, harga_sewa_per_hari, status

Catatan
- Password akun disimpan menggunakan password_hash.
- Foto kostum memakai assets/placeholder.svg karena tabel kostum yang ada belum memiliki kolom foto.
- Tombol detail memberi informasi kostum; proses transaksi dan pembayaran belum diimplementasikan.
- Untuk deployment online, gunakan HTTPS, akun database khusus, CSRF token, dan pembatasan percobaan login.
