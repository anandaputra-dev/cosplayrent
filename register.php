<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$peranDipilih = $_POST['peran'] ?? 'pengguna';
if (!in_array($peranDipilih, ['pengguna', 'admin'], true)) {
    $peranDipilih = 'pengguna';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['kata_sandi'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Nama dan email yang valid wajib diisi.';
    } elseif (strlen($password) < 8) {
        $error = 'Kata sandi minimal 8 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi kata sandi tidak sama.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO pengguna (nama_lengkap,email,kata_sandi,peran) VALUES (?,?,?,?)');
            $stmt->execute([
                $nama,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $peranDipilih
            ]);
            header('Location: login.php?daftar=berhasil');
            exit;
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000' ? 'Email sudah terdaftar.' : 'Pendaftaran gagal. Coba lagi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Daftar | COSPLAYRENT</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .auth-wrap { min-height:80vh; display:grid; place-items:center; padding:40px 18px; background:#faf7ff }
        .auth-card { width:min(460px,100%); background:white; padding:32px; border:1px solid #ece8f4; border-radius:20px; box-shadow:0 22px 60px #2c144d12; box-sizing:border-box }
        .auth-card h1 { font-size:30px; margin:8px 0 }
        .auth-card p { color:#77738b; font-size:13px; line-height:1.7 }
        .auth-card label { display:block; font-size:12px; font-weight:700; margin:15px 0 6px }
        .auth-card input, .auth-card select { display:block; box-sizing:border-box; width:100%; min-height:44px; padding:12px 13px; border:1px solid #dcd5e9; border-radius:9px; background:#fff; color:#171329; font:inherit }
        .auth-card input:focus, .auth-card select:focus { outline:2px solid #8b35e8; outline-offset:1px; border-color:#8b35e8 }
        .auth-card .btn { width:100%; border:0; margin-top:20px; cursor:pointer }
        .notice { padding:12px; border-radius:8px; background:#fff0f0; color:#a12626; font-size:13px; margin:12px 0 }
        .auth-foot { font-size:12px; text-align:center; margin-top:18px }
        .auth-brand { font-weight:800; color:#8b35e8 }
        .role-help { margin:7px 0 0; font-size:12px; color:#77738b }
        @media(max-width:480px) { .auth-card { padding:24px 20px } }
    </style>
</head>
<body>
<header class="header">
    <a class="brand" href="index.php"><span class="brand-mark">C</span><span>COSPLAY<span class="brand-accent">RENT</span><small>KARAKTERMU, MOMENMU</small></span></a>
    <a href="index.php" class="login-link">← Beranda</a>
</header>
<main class="auth-wrap">
    <form class="auth-card" method="post">
        <span class="eyebrow">GABUNG BERSAMA KAMI</span>
        <h1>Buat akunmu ✦</h1>
        <p>Daftar untuk menjelajahi dunia kostum COSPLAYRENT.</p>
        <?php if ($error): ?><div class="notice"><?= e($error) ?></div><?php endif; ?>

        <label for="nama_lengkap">Nama lengkap</label>
        <input id="nama_lengkap" name="nama_lengkap" maxlength="100" required autocomplete="name" value="<?= e($_POST['nama_lengkap'] ?? '') ?>">

        <label for="email">Email</label>
        <input id="email" type="email" name="email" required autocomplete="email" value="<?= e($_POST['email'] ?? '') ?>">

        <label for="peran">Daftar sebagai</label>
        <select id="peran" name="peran" required>
            <option value="pengguna" <?= $peranDipilih === 'pengguna' ? 'selected' : '' ?>>Pelanggan</option>
            <option value="admin" <?= $peranDipilih === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        <p class="role-help">Pelanggan dapat melihat galeri. Admin dapat mengelola data dan foto kostum.</p>

        <label for="kata_sandi">Kata sandi (minimal 8 karakter)</label>
        <input id="kata_sandi" type="password" name="kata_sandi" minlength="8" required autocomplete="new-password">

        <label for="konfirmasi">Konfirmasi kata sandi</label>
        <input id="konfirmasi" type="password" name="konfirmasi" minlength="8" required autocomplete="new-password">

        <button class="btn" type="submit">Daftar sekarang ↗</button>
        <div class="auth-foot">Sudah punya akun? <a class="auth-brand" href="login.php">Masuk</a></div>
    </form>
</main>

</body>
</html>
