<?php
require_once 'koneksi.php';

$message = '';

// KODE UNTUK REGISTER
if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['kata_sandi'], PASSWORD_BCRYPT);
    $telepon  = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);

    $check = mysqli_query($conn, "SELECT id FROM pengguna WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO pengguna (nama_lengkap, email, kata_sandi, nomor_telepon, peran) 
                  VALUES ('$nama', '$email', '$password', '$telepon', 'pelanggan')";
        if (mysqli_query($conn, $query)) {
            $message = "Registrasi berhasil! Silakan login.";
        } else {
            $message = "Gagal mendaftar.";
        }
    }
}

// KODE UNTUK LOGIN
if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['kata_sandi'];

    $result = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email'");
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['kata_sandi'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama_lengkap'];
            $_SESSION['peran']   = $user['peran'];
            header("Location: dashboard.php");
            exit;
        }
    }
    $message = "Email atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CosplayRent - Masuk & Daftar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0b0719; color: #fff; }
        .card-custom { background: #130d2a; border: 1px solid #2a1b54; border-radius: 16px; }
        .btn-neon { background: linear-gradient(90deg, #b000ff, #00d4ff); color: white; border: none; font-weight: bold; }
        .btn-neon:hover { color: white; opacity: 0.9; }
        .form-control { background: #0b0719; border: 1px solid #2a1b54; color: #fff; }
        .form-control:focus { background: #0b0719; color: #fff; border-color: #b000ff; box-shadow: none; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
<div class="container" style="max-width: 450px;">
    <div class="card card-custom p-4 shadow-lg">
        <h3 class="text-center text-primary mb-3 fw-bold" style="background: linear-gradient(90deg, #00d4ff, #b000ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">COSPLAYRENT</h3>
        
        <?php if($message): ?>
            <div class="alert alert-info py-2 fs-6"><?= $message; ?></div>
        <?php endif; ?>

        <!-- NAV TABS -->
        <ul class="nav nav-pills nav-justified mb-4" id="pills-tab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active text-white" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login">Masuk</button>
            </li>
            <li class="nav-item">
                <button class="nav-link text-white" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register">Daftar</button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- FORM LOGIN -->
            <div class="tab-pane fade show active" id="pills-login">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kata Sandi</label>
                        <input type="password" name="kata_sandi" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-neon w-100 py-2 mt-2">Masuk</button>
                </form>
            </div>

            <!-- FORM REGISTER -->
            <div class="tab-pane fade" id="pills-register">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" name="nomor_telepon" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kata Sandi</label>
                        <input type="password" name="kata_sandi" class="form-control" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-neon w-100 py-2 mt-2">Daftar Akun</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>