
<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';

// Pengguna wajib login.
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data pengguna dari database.
$stmt = $pdo->prepare("
    SELECT id, nama_lengkap, email, peran
    FROM pengguna
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Pengguna | COSPLAYRENT</title>
    <link rel="stylesheet" href="assets/style.css">

    <style>
        .dashboard { padding: 55px 0 80px; }
        .welcome-card {
            padding: 35px;
            border-radius: 22px;
            background: linear-gradient(135deg, #8b35e8, #b879f5);
            color: white;
            margin-bottom: 30px;
        }
        .welcome-card p { line-height: 1.7; }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .dashboard-card {
            padding: 25px;
            background: white;
            border: 1px solid #eee7f7;
            border-radius: 18px;
        }
        .dashboard-card p {
            color: #77738b;
            overflow-wrap: anywhere;
        }
        .dashboard-icon {
            font-size: 30px;
            color: #8b35e8;
        }
    </style>
</head>
<body>

<header class="header">
    <a class="brand" href="index.php">
        <span class="brand-mark">C</span>
        <span>COSPLAY<span class="brand-accent">RENT</span>
            <small>KARAKTERMU, MOMENMU</small>
        </span>
    </a>

    <div class="nav-actions">
        <a class="login-link" href="gallery.php">Galeri Kostum</a>
        <a class="btn btn-small" href="logout.php">Keluar</a>
    </div>
</header>

<main class="section-wrap dashboard">
    <section class="welcome-card">
        <span>✦ DASHBOARD PENGGUNA</span>

        <h1>
            Halo, <?= e($user['nama_lengkap']) ?>!
        </h1>

        <p>
            Selamat datang di COSPLAYRENT.
            Temukan kostum favoritmu untuk acara spesial berikutnya.
        </p>

        <a class="btn" href="gallery.php"
           style="background:white;color:#8b35e8">
            Jelajahi Galeri ↗
        </a>
    </section>

    <h2>Informasi Akun</h2>

    <section class="dashboard-grid">
        <article class="dashboard-card">
            <div class="dashboard-icon">👤</div>
            <h3>Nama Pengguna</h3>
            <p><?= e($user['nama_lengkap']) ?></p>
        </article>

        <article class="dashboard-card">
            <div class="dashboard-icon">✉</div>
            <h3>Email</h3>
            <p><?= e($user['email']) ?></p>
        </article>

        <article class="dashboard-card">
            <div class="dashboard-icon">✦</div>
            <h3>Jenis Akun</h3>
            <p><?= e(ucfirst($user['peran'] ?? 'pengguna')) ?></p>
        </article>
    </section>

    <?php if (($user['peran'] ?? '') === 'admin'): ?>
    <section class="dashboard-card" style="margin-top:25px;border-color:#d9c2fb">
        <h2>Panel Admin</h2>
        <p>Kelola data kostum dan unggah foto galeri tanpa membuka file kode.</p>
        <a class="btn" href="admin/kostum.php">Kelola Kostum ↗</a>
    </section>
    <?php endif; ?>

    <section class="dashboard-card" style="margin-top:25px">
        <h2>Mulai Jelajahi Kostum</h2>
        <p>Lihat koleksi kostum yang tersedia di COSPLAYRENT.</p>
        <a class="btn" href="gallery.php">Lihat Galeri Kostum</a>
    </section>
</main>

</body>
</html>
