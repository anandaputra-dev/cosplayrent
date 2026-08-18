<?php
require_once 'koneksi.php';
require_once 'booking_functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

$penyewaan_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$penyewaan = getPenyewaanById($conn, $penyewaan_id, (int) $_SESSION['user_id']);

if (!$penyewaan) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Berhasil - CosplayRent</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="glass-card p-4 p-md-5 text-center">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                <h3 class="title-fantasy mt-3 mb-1">Booking <span class="text-gradient">Berhasil Dibuat</span></h3>
                <p class="text-secondary mb-4">Pesananmu #RENT-<?= $penyewaan['id'] ?> sedang menunggu konfirmasi admin.</p>

                <div class="glass-card p-4 text-start mb-4">
                    <?php foreach ($penyewaan['items'] as $item): ?>
                        <div class="d-flex justify-content-between border-bottom border-secondary border-opacity-25 pb-2 mb-2">
                            <div>
                                <div class="fw-bold"><?= htmlspecialchars($item['nama_kostum']) ?></div>
                                <small class="text-secondary"><?= $item['jumlah_hari'] ?> hari × Rp <?= number_format($item['harga_sewa_per_hari'], 0, ',', '.') ?></small>
                            </div>
                            <div class="fw-bold">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></div>
                        </div>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between small text-secondary mt-3">
                        <span>Tanggal Sewa</span>
                        <span><?= $penyewaan['tanggal_mulai'] ?> s/d <?= $penyewaan['tanggal_selesai'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between fs-5 fw-bold mt-2">
                        <span>Total Biaya</span>
                        <span class="text-gradient">Rp <?= number_format($penyewaan['total_biaya_sewa'], 0, ',', '.') ?></span>
                    </div>
                </div>

                <p class="text-secondary small mb-4">
                    Status pembayaran: <span class="badge bg-warning text-dark"><?= ucfirst(str_replace('_', ' ', $penyewaan['status_pembayaran'])) ?></span>
                    &nbsp;|&nbsp;
                    Status sewa: <span class="badge bg-info text-dark"><?= ucfirst(str_replace('_', ' ', $penyewaan['status_sewa'])) ?></span>
                </p>

                <div class="d-flex gap-2 justify-content-center">
                    <a href="dashboard.php" class="btn btn-neon px-4">Lihat Riwayat Sewa</a>
                    <a href="index.php#katalog" class="btn btn-outline-custom px-4">Sewa Kostum Lain</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>