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
    <title>Dashboard Pelanggan - CosplayRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #070510; color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar { background: #0f0a21; border-right: 1px solid rgba(176,0,255,0.2); min-height: 100vh; }
        .glass-card { background: rgba(20, 14, 40, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(176,0,255,0.2); border-radius: 16px; }
        .nav-link.active { background: linear-gradient(90deg, #b000ff, #7900ff) !important; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar p-4">
            <h4 class="fw-bold text-info mb-4">COSPLAY<span class="text-white">RENT</span></h4>
            <div class="nav flex-column nav-pills gap-2">
                <a class="nav-link active text-white" href="#"><i class="bi bi-clock-history me-2"></i>Riwayat Sewa</a>
                <a class="nav-link text-secondary" href="index.php#katalog"><i class="bi bi-grid me-2"></i>Katalog Kostum</a>
                <a class="nav-link text-danger mt-5" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="col-md-10 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Halo, <span style="color:#00d4ff"><?= $_SESSION['nama']; ?></span> 👋</h2>
                    <p class="text-secondary">Kelola pesanan dan jadwal sewa kostummu di sini.</p>
                </div>
            </div>

            <div class="glass-card p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-receipt me-2 text-primary"></i>Pesanan Aktif & Riwayat</h5>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr class="text-secondary">
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
                                    <tr>
                                        <td class="fw-bold text-info">#RENT-<?= $row['id'] ?></td>
                                        <td><?= $row['tanggal_mulai'] ?> s/d <?= $row['tanggal_selesai'] ?></td>
                                        <td class="fw-bold">Rp <?= number_format($row['total_biaya_sewa'], 0, ',', '.') ?></td>
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

</body>
</html>