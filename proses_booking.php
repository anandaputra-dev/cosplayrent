<?php
require_once 'koneksi.php';
require_once 'booking_functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php#katalog");
    exit;
}

$pengguna_id     = (int) $_SESSION['user_id'];
$kostum_id       = (int) ($_POST['kostum_id'] ?? 0);
$tanggal_mulai   = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';

$kostum = getKostumById($conn, $kostum_id);
if (!$kostum) {
    header("Location: index.php#katalog");
    exit;
}

//Validasi format & logika tanggal
$hari_ini = date('Y-m-d');
$tanggal_valid = strtotime($tanggal_mulai) && strtotime($tanggal_selesai)
    && $tanggal_mulai >= $hari_ini
    && $tanggal_selesai >= $tanggal_mulai;

if (!$tanggal_valid) {
    header("Location: booking.php?id=$kostum_id&error=invalid");
    exit;
}

//Cek ketersediaan (tidak bentrok dengan booking lain)
$tersedia = cekKetersediaanKostum($conn, $kostum_id, $tanggal_mulai, $tanggal_selesai);
if (!$tersedia) {
    header("Location: booking.php?id=$kostum_id&error=bentrok");
    exit;
}

//Hitung jumlah hari & total biaya
$jumlah_hari    = hitungJumlahHari($tanggal_mulai, $tanggal_selesai);
$harga_per_hari = (float) $kostum['harga_sewa_per_hari'];
$total_biaya    = hitungTotalBiaya($harga_per_hari, $jumlah_hari);

//Simpan ke database (header penyewaan + detail item)
mysqli_begin_transaction($conn);
try {
    $penyewaan_id = buatPenyewaan($conn, $pengguna_id, $tanggal_mulai, $tanggal_selesai, $total_biaya);
    tambahDetailPenyewaan($conn, $penyewaan_id, $kostum_id, $harga_per_hari, $jumlah_hari, $total_biaya);

    mysqli_commit($conn);
} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: booking.php?id=$kostum_id&error=invalid");
    exit;
}

//Arahkan ke halaman konfirmasi
header("Location: konfirmasi_booking.php?id=$penyewaan_id");
exit;