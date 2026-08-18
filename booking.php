<?php
require_once 'koneksi.php';
require_once 'booking_functions.php';

// Wajib login untuk booking
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

$kostum_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$kostum = getKostumById($conn, $kostum_id);

if (!$kostum) {
    header("Location: index.php#katalog");
    exit;
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking <?= htmlspecialchars($kostum['nama_kostum']) ?> - CosplayRent</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <a href="index.php#katalog" class="text-secondary small text-decoration-none d-inline-block mb-3">← Kembali ke Katalog</a>

            <div class="glass-card p-4 p-md-5">
                <h3 class="title-fantasy mb-4">Booking <span class="text-gradient">Kostum</span></h3>

                <?php if ($error === 'bentrok'): ?>
                    <div class="alert alert-danger small">
                        Maaf, kostum ini sudah dibooking pada rentang tanggal yang kamu pilih. Silakan pilih tanggal lain.
                    </div>
                <?php elseif ($error === 'invalid'): ?>
                    <div class="alert alert-danger small">
                        Tanggal tidak valid. Tanggal selesai harus setelah atau sama dengan tanggal mulai, dan tidak boleh tanggal yang sudah lewat.
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <!-- INFO KOSTUM -->
                    <div class="col-md-5">
                        <?php $gambar = !empty($kostum['foto_url']) ? $kostum['foto_url'] : 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=500'; ?>
                        <img src="<?= htmlspecialchars($gambar) ?>" class="img-fluid rounded-4 mb-3" style="height: 300px; object-fit: cover; width: 100%;" alt="<?= htmlspecialchars($kostum['nama_kostum']) ?>">
                        <span class="badge bg-secondary bg-opacity-25 text-info mb-2"><?= htmlspecialchars($kostum['nama_kategori'] ?? 'Cosplay') ?></span>
                        <h4 class="fw-bold"><?= htmlspecialchars($kostum['nama_kostum']) ?></h4>
                        <p class="text-secondary small mb-1">Karakter: <?= htmlspecialchars($kostum['nama_karakter'] ?? '-') ?></p>
                        <p class="text-secondary small mb-3">Ukuran: <?= htmlspecialchars($kostum['ukuran']) ?></p>
                        <div class="fs-4 fw-bold text-gradient">
                            Rp <?= number_format($kostum['harga_sewa_per_hari'], 0, ',', '.') ?>
                            <small class="text-secondary fs-6">/hari</small>
                        </div>
                    </div>

                    <!-- FORM BOOKING -->
                    <div class="col-md-7">
                        <form method="POST" action="proses_booking.php" id="formBooking">
                            <input type="hidden" name="kostum_id" value="<?= $kostum['id'] ?>">
                            <input type="hidden" name="harga_per_hari" value="<?= $kostum['harga_sewa_per_hari'] ?>" id="hargaPerHari">

                            <div class="mb-3">
                                <label class="form-label text-secondary small">Tanggal Mulai Sewa</label>
                                <input type="date" name="tanggal_mulai" id="tanggalMulai" class="form-control" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-secondary small">Tanggal Selesai Sewa</label>
                                <input type="date" name="tanggal_selesai" id="tanggalSelesai" class="form-control" required min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="glass-card p-3 mb-4">
                                <div class="d-flex justify-content-between small text-secondary mb-1">
                                    <span>Jumlah hari</span>
                                    <span id="jumlahHari">-</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Estimasi Total</span>
                                    <span class="text-gradient" id="totalBiaya">Rp 0</span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-neon w-100 py-2">Konfirmasi Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Estimasi biaya real-time di sisi client (perhitungan final tetap divalidasi di server)
    const hargaPerHari  = parseFloat(document.getElementById('hargaPerHari').value);
    const tanggalMulai  = document.getElementById('tanggalMulai');
    const tanggalSelesai = document.getElementById('tanggalSelesai');
    const jumlahHariEl  = document.getElementById('jumlahHari');
    const totalBiayaEl  = document.getElementById('totalBiaya');

    function formatRupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    function hitungEstimasi() {
        if (!tanggalMulai.value || !tanggalSelesai.value) return;

        const mulai   = new Date(tanggalMulai.value);
        const selesai = new Date(tanggalSelesai.value);
        const selisihMs = selesai - mulai;
        const jumlahHari = Math.floor(selisihMs / (1000 * 60 * 60 * 24)) + 1;

        if (jumlahHari > 0) {
            jumlahHariEl.textContent = jumlahHari + ' hari';
            totalBiayaEl.textContent = formatRupiah(jumlahHari * hargaPerHari);
        } else {
            jumlahHariEl.textContent = '-';
            totalBiayaEl.textContent = 'Rp 0';
        }
    }

    tanggalMulai.addEventListener('change', () => {
        tanggalSelesai.min = tanggalMulai.value;
        hitungEstimasi();
    });
    tanggalSelesai.addEventListener('change', hitungEstimasi);
</script>
</body>
</html>