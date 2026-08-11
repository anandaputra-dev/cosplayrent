<?php
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT p.*, count(dp.id) as total_item 
          FROM penyewaan p 
          LEFT JOIN detail_penyewaan dp ON p.id = dp.penyewaan_id 
          WHERE p.pengguna_id = '$user_id' 
          GROUP BY p.id ORDER BY p.dibuat_pada DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CosplayRent</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-2 p-4 min-vh-100 border-end border-secondary border-opacity-25" style="background: var(--nav-bg);">
            <a href="index.php" class="text-decoration-none">
                <h4 class="fw-bold tracking-wider mb-4">COSPLAY<span style="color:var(--neon-purple)">RENT</span></h4>
            </a>
            <div class="nav flex-column nav-pills gap-2">
                <a class="nav-link active" href="#"><i class="bi bi-clock-history me-2"></i>Riwayat Sewa</a>
                <a class="nav-link" href="index.php#katalog"><i class="bi bi-grid me-2"></i>Katalog Kostum</a>
                <a class="nav-link text-danger mt-5" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a>
            </div>
        </div>

        <!-- CONTENT AREA -->
        <div class="col-md-10 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="title-fantasy mb-1">Halo, <span class="text-gradient"><?= $_SESSION['nama']; ?></span> 👋</h2>
                    <p class="text-secondary">Kelola pesanan dan riwayat sewa kostummu di sini.</p>
                </div>
                <button class="btn rounded-circle d-flex align-items-center justify-content-center p-0" id="themeToggle" style="width: 42px; height: 42px;">
                    <i class="bi bi-sun-fill text-warning"></i>
                </button>
            </div>

            <div class="glass-card p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-receipt me-2 text-info"></i>Pesanan Aktif & Riwayat</h5>
                <div class="table-responsive">
                    <table class="table align-middle text-nowrap" style="color: var(--text-primary);">
                        <thead>
                            <tr class="text-secondary border-bottom border-secondary border-opacity-25">
                                <th>#ID PESANAN</th>
                                <th>TANGGAL SEWA</th>
                                <th>TOTAL BIAYA</th>
                                <th>PEMBAYARAN</th>
                                <th>STATUS SEWA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr class="border-bottom border-secondary border-opacity-10">
                                        <td class="fw-bold text-info">#RENT-<?= $row['id'] ?></td>
                                        <td><?= $row['tanggal_mulai'] ?> s/d <?= $row['tanggal_selesai'] ?></td>
                                        <td class="fw-bold text-gradient">Rp <?= number_format($row['total_biaya_sewa'], 0, ',', '.') ?></td>
                                        <td><span class="badge bg-warning text-dark"><?= ucfirst($row['status_pembayaran']) ?></span></td>
                                        <td><span class="badge bg-info text-dark"><?= ucfirst($row['status_sewa']) ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-secondary">Belum ada transaksi penyewaan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const themeToggleBtn = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;

        const savedTheme = localStorage.getItem('cosplayrent_theme') || 'dark';
        setTheme(savedTheme);

        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });

        function setTheme(theme) {
            htmlElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('cosplayrent_theme', theme);
            themeToggleBtn.innerHTML = theme === 'light' ? 
                '<i class="bi bi-moon-stars-fill" style="color: #ff9800;"></i>' : 
                '<i class="bi bi-sun-fill" style="color: #ffc107;"></i>';
        }
    });
</script>
</body>
</html>