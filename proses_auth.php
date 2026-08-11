<?php
require_once 'koneksi.php';

$aksi = $_GET['aksi'] ?? '';

if ($aksi == 'register') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    $password = password_hash($_POST['kata_sandi'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO pengguna (nama_lengkap, email, kata_sandi, nomor_telepon, peran) 
            VALUES ('$nama', '$email', '$password', '$telepon', 'pelanggan')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal Registrasi / Email sudah terdaftar.'); window.location='index.php';</script>";
    }
} 

elseif ($aksi == 'login') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['kata_sandi'];

    $result = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email'");
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['kata_sandi'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama_lengkap'];
            $_SESSION['peran'] = $user['peran'];
            header("Location: dashboard.php");
            exit;
        }
    }
    echo "<script>alert('Email atau Password salah!'); window.location='index.php';</script>";
}
?>