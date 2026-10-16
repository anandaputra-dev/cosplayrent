
<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['kata_sandi'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT id, nama_lengkap, email, kata_sandi, peran
         FROM pengguna
         WHERE email = ?
         LIMIT 1"
    );

    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['kata_sandi'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama_lengkap'];
        $_SESSION['peran'] = $user['peran'];

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Email atau kata sandi tidak sesuai.';
}
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Masuk | COSPLAYRENT</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .auth-wrap {
            min-height: 80vh;
            display: grid;
            place-items: center;
            padding: 40px 18px;
            background: #faf7ff
        }

        .auth-card {
            width: min(460px, 100%);
            background: white;
            padding: 32px;
            border: 1px solid #ece8f4;
            border-radius: 20px;
            box-shadow: 0 22px 60px #2c144d12
        }

        .auth-card h1 {
            font-size: 30px;
            margin: 8px 0
        }

        .auth-card p {
            color: #77738b;
            font-size: 13px;
            line-height: 1.7
        }

        .auth-card label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            margin: 15px 0 6px
        }

        .auth-card input {
            width: 100%;
            padding: 13px;
            border: 1px solid #ece8f4;
            border-radius: 9px;
            font: inherit
        }

        .auth-card .btn {
            width: 100%;
            border: 0;
            margin-top: 20px
        }

        .notice {
            padding: 12px;
            border-radius: 8px;
            background: #fff0f0;
            color: #a12626;
            font-size: 13px
        }

        .success {
            background: #eaf9f0;
            color: #216e45
        }

        .auth-foot {
            font-size: 12px;
            text-align: center;
            margin-top: 18px
        }

        .auth-brand {
            font-weight: 800;
            color: #8b35e8
        }
    </style>
</head>

<body>
    <header class="header"><a class="brand" href="index.php"><span class="brand-mark">C</span><span>COSPLAY<span
                    class="brand-accent">RENT</span><small>KARAKTERMU, MOMENMU</small></span></a><a href="index.php"
            class="login-link">← Beranda</a></header>
    <main class="auth-wrap">
        <form class="auth-card" method="post"><span class="eyebrow">SELAMAT DATANG KEMBALI</span>
            <h1>Masuk ke akunmu</h1>
            <p>Lanjutkan petualangan cosplay-mu.</p><?php if (isset($_GET['daftar'])): ?>
                <div class="notice success">Pendaftaran berhasil. Silakan masuk.</div><?php endif; ?><?php if ($error): ?>
                <div class="notice"><?= e($error) ?></div><?php endif; ?>
            <label for="email">Email</label><input id="email" type="email" name="email" required autocomplete="email"
                value="<?= e($_POST['email'] ?? '') ?>">
            <label for="kata_sandi">Kata sandi</label><input id="kata_sandi" type="password" name="kata_sandi" required
                autocomplete="current-password">
            <button class="btn" type="submit">Masuk ↗</button>
            <div class="auth-foot">Belum punya akun? <a class="auth-brand" href="register.php">Daftar</a></div>
        </form>
    </main>
</body>

</html>