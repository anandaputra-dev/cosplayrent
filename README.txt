COSPLAYRENT - PHP Native + MySQL
================================

ISI
- index.php       : landing page desain asli, katalog mengambil data dari database
- register.php    : pendaftaran pengguna
- login.php       : login dengan password_hash/password_verify dan session
- dashboard.php   : dashboard dasar pengguna
- gallery.php     : galeri kostum dari database
- detail.php      : detail kostum
- logout.php      : keluar akun
- config/database.php : konfigurasi koneksi PDO
- database.sql    : database, tabel dan data kostum contoh
- assets/         : CSS dan JavaScript desain yang dikirim

CARA MENJALANKAN (XAMPP Windows)
1. Ekstrak folder cosplayrent_php ke C:\xampp\htdocs\cosplayrent
2. Buka XAMPP Control Panel, jalankan Apache dan MySQL.
3. Buka http://localhost/phpmyadmin
4. Pilih tab Import, pilih file database.sql dari folder proyek, lalu klik Import/Go.
   Alternatif: buka tab SQL dan jalankan isi database.sql.
5. Jika MySQL root memiliki password, ubah $password di config/database.php.
6. Buka http://localhost/cosplayrent/

CATATAN
- Buat akun lewat halaman Register. Password disimpan dalam bentuk hash.
- Jangan membuat akun admin dengan mengubah field form. Peran default adalah pengguna.
- Data foto/kostum di database adalah data contoh; ganti foto dan stok sesuai inventaris nyata.
- Tombol detail menampilkan info dan tautan untuk bertanya; transaksi/pembayaran belum dibuat.
- Untuk deployment online, gunakan HTTPS, user database khusus (bukan root), CSRF token,
  pembatasan percobaan login, dan konfigurasi error yang aman.

GALERI + FILTER KOTA (DATABASE YANG SUDAH ADA)
1. Backup database rental_kostum terlebih dahulu di phpMyAdmin (Export).
2. Pilih database rental_kostum > tab Import, lalu jalankan database_gallery_kota.sql.
   File ini menambahkan kolom foto dan kota bila belum ada; tidak menghapus data kostum.
3. Jika kolom foto/kota belum dibuat, landing page tetap bisa dibuka, tetapi filter kota baru aktif setelah migrasi dijalankan.
3. Isi kolom kota untuk tiap kostum dari tab Browse/Edit pada tabel kostum.
4. Isi kolom foto dengan path gambar lokal, misalnya uploads/naruto.jpg, atau URL gambar https://...
   Untuk gambar lokal, buat folder uploads di folder proyek dan letakkan file gambar di sana.
5. Buka gallery.php. Filter kota akan mengambil daftar kota langsung dari database.

CATATAN STRUKTUR DATABASE
Kode katalog menggunakan kolom yang sesuai dengan struktur rental_kostum: kategori.nama_kategori,
kostum.nama_karakter, nama_serial, ukuran, harga_sewa_per_hari, status. Kolom foto dan kota
ditambahkan lewat database_gallery_kota.sql.