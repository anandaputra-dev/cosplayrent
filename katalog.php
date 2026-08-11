<?php
require_once 'koneksi.php';

// Ambil data filter jika ada
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$kota_filter     = isset($_GET['kota']) ? $_GET['kota'] : '';

// Query dasar
$sql = "SELECT k.*, cat.nama_kategori 
        FROM kostum k 
        LEFT JOIN kategori cat ON k.kategori_id = cat.id 
        WHERE 1=1";

if (!empty($kategori_filter)) {
    $sql .= " AND cat.nama_kategori = '$kategori_filter'";
}
if (!empty($kota_filter)) {
    // Diasumsikan lokasi tersimpan dalam deskripsi atau kolom alamat
    $sql .= " AND k.deskripsi LIKE '%$kota_filter%'";
}

$query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CosplayRent - Katalog & Filter Kota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0b0719; color: #ffffff; }
        .navbar-custom { background-color: #0b0719; border-bottom: 1px solid #1f153a; }
        .card-kostum { background-color: #130d2a; border: 1px solid #2a1b54; border-radius: 12px; overflow: hidden; transition: 0.3s; }
        .card-kostum:hover { transform: translateY(-5px); border-color: #b000ff; }
        .badge-status { background-color: #00ff88; color: #000; font-weight: bold; position: absolute; top: 10px; right: 10px; }
        .badge-kategori { background-color: #2a1b54; color: #00d4ff; font-size: 0.8rem; }
        .btn-purple { background: #b000ff; color: #fff; }
        .btn-purple:hover { background: #8e00ce; color: #fff; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="#">
            <span style="color:#00d4ff">COSPLAY</span><span style="color:#b000ff">RENT</span>
        </a>
        <div class="d-flex">
            <a href="dashboard.php" class="btn btn-outline-light me-2">Dashboard</a>
            <a href="auth.php" class="btn btn-purple">Sewa Sekarang</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <!-- TITLE -->
    <div class="text-center mb-4">
        <p class="text-uppercase tracking-widest text-secondary mb-1">— KOLEKSI PILIHAN —</p>
        <h1 class="fw-bold fs-2">KATALOG <span style="color:#00d4ff">KOSTUM</span></h1>
    </div>

    <!-- FILTER BAR (KATEGORI & KOTA) -->
    <form method="GET" action="" class="row g-3 justify-content-center mb-5">
        <div class="col-md-3">
            <select name="kota" class="form-select bg-dark text-white border-secondary" onchange="this.form.submit()">
                <option value="">-- Semua Kota --</option>
                <option value="Jakarta" <?= $kota_filter == 'Jakarta' ? 'selected' : '' ?>>Jakarta</option>
                <option value="Bandung" <?= $kota_filter == 'Bandung' ? 'selected' : '' ?>>Bandung</option>
                <option value="Yogyakarta" <?= $kota_filter == 'Yogyakarta' ? 'selected' : '' ?>>Yogyakarta</option>
                <option value="Surabaya" <?= $kota_filter == 'Surabaya' ? 'selected' : '' ?>>Surabaya</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="kategori" class="form-select bg-dark text-white border-secondary" onchange="this.form.submit()">
                <option value="">-- Semua Kategori --</option>
                <option value="Anime" <?= $kategori_filter == 'Anime' ? 'selected' : '' ?>>Anime</option>
                <option value="Fantasy" <?= $kategori_filter == 'Fantasy' ? 'selected' : '' ?>>Fantasy</option>
                <option value="Game" <?= $kategori_filter == 'Game' ? 'selected' : '' ?>>Game</option>
            </select>
        </div>
    </form>

    <!-- GALLERY GRID KOSTUM -->
    <div class="row g-4">
        <?php if(mysqli_num_rows($query) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query)): ?>
                <div class="col-md-4">
                    <div class="card card-kostum position-relative h-100">
                        <span class="badge badge-status rounded-pill px-3 py-2">
                            <?= ucfirst($row['status']) ?>
                        </span>
                        <!-- Placeholder gambar (Atur sesuai URL Foto kamu) -->
                        <img src="https://images.unsplash.com/photo-1534447677768-be436bb09401?w=500" class="card-img-top" alt="Kostum" style="height: 250px; object-fit: cover;">
                        
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge badge-kategori mb-2"><?= $row['nama_kategori'] ?? 'Umum' ?></span>
                                <h5 class="card-title fw-bold text-white"><?= $row['nama_kostum'] ?></h5>
                                <p class="text-muted small"> Ukuran: <?= $row['ukuran'] ?> | Character: <?= $row['nama_karakter'] ?></p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-3">
                                <div>
                                    <span class="fs-5 fw-bold text-primary">Rp <?= number_format($row['harga_sewa_per_hari'], 0, ',', '.') ?></span>
                                    <small class="text-muted">/hari</small>
                                </div>
                                <a href="auth.php" class="btn btn-purple btn-sm px-3">Sewa</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted">
                <p>Tidak ada kostum yang cocok dengan filter yang dipilih.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>