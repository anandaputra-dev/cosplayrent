<?php
require_once 'koneksi.php';

// Filter Kategori & Kota
$kategori_filter = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';
$kota_filter     = isset($_GET['kota']) ? mysqli_real_escape_string($conn, $_GET['kota']) : '';

$sql = "SELECT k.*, cat.nama_kategori 
        FROM kostum k 
        LEFT JOIN kategori cat ON k.kategori_id = cat.id 
        WHERE 1=1";

if (!empty($kategori_filter)) {
    $sql .= " AND cat.nama_kategori = '$kategori_filter'";
}
if (!empty($kota_filter)) {
    $sql .= " AND k.deskripsi LIKE '%$kota_filter%'";
}

$query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CosplayRent - Wujudkan Karakter Impianmu</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- CSS KUSTOM BARU -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NAVBAR PRESISI FIGMA -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="index.php">
            <div class="p-2 rounded-3" style="background: linear-gradient(135deg, var(--neon-cyan), var(--neon-purple));">
                <i class="bi bi-mask text-white"></i>
            </div>
            <span class="fs-4 tracking-wider">COSPLAY<span style="color:var(--neon-purple)">RENT</span></span>
        </a>
        
        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active px-3" href="#beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#katalog">Katalog</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#cara-sewa">Cara Sewa</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#event">Event</a></li>
            </ul>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- TOGGLE LIGHT/DARK MODE -->
            <button class="btn rounded-circle d-flex align-items-center justify-content-center p-0" 
                    id="themeToggle" 
                    style="width: 42px; height: 42px; transition: all 0.3s ease;">
                <i class="bi bi-sun-fill text-warning"></i>
            </button>
            <a href="auth.php" class="btn btn-neon">Sewa Sekarang</a>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<section id="beranda" class="py-5 my-4">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge-pill-custom text-uppercase fw-bold mb-3 d-inline-block">
                    • 500+ Kostum Tersedia
                </span>
                <h1 class="title-fantasy display-3 mb-3">
                    WUJUDKAN<br>
                    <span class="text-gradient">KARAKTER</span><br>
                    IMPIANMU
                </h1>
                <p class="text-secondary fs-5 mb-4" style="max-width: 500px;">
                    Dari anime legendaris hingga ksatria fantasi — kami menyediakan kostum cosplay premium berkualitas tinggi untuk event, pemotretan, atau sekadar tampil keren.
                </p>
                <div class="d-flex gap-3 mb-5">
                    <a href="#katalog" class="btn btn-neon fs-6">Jelajahi Katalog →</a>
                    <a href="#cara-sewa" class="btn btn-outline-custom fs-6">Cara Sewa</a>
                </div>

                <!-- STATS COUNTER -->
                <div class="row pt-4 border-top border-secondary border-opacity-25">
                    <div class="col-3">
                        <h3 class="fw-bold text-gradient mb-0">500+</h3>
                        <small class="text-secondary">Kostum</small>
                    </div>
                    <div class="col-3">
                        <h3 class="fw-bold text-gradient mb-0">2000+</h3>
                        <small class="text-secondary">Pelanggan</small>
                    </div>
                    <div class="col-3">
                        <h3 class="fw-bold text-gradient mb-0">98%</h3>
                        <small class="text-secondary">Puas</small>
                    </div>
                    <div class="col-3">
                        <h3 class="fw-bold text-gradient mb-0">5★</h3>
                        <small class="text-secondary">Rating</small>
                    </div>
                </div>
            </div>

            <!-- HERO GALLERY STAGGERED -->
            <div class="col-lg-6">
                <div class="row g-3 position-relative">
                    <div class="col-7 position-relative">
                        <div class="hero-img-box shadow-lg">
                            <img src="https://images.unsplash.com/photo-1534447677768-be436bb09401?w=600" class="img-fluid rounded-4" style="height: 420px; object-fit: cover;" alt="Cosplay 1">
                        </div>
                        <div class="floating-stat-box">
                            <small class="text-uppercase text-secondary tracking-wider d-block" style="font-size: 10px;">TERSEWA HARI INI</small>
                            <span class="fs-3 fw-bold text-gradient">47</span>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="hero-img-box mb-3 shadow-lg">
                            <img src="https://images.unsplash.com/photo-1563089145-599997674d42?w=400" class="img-fluid rounded-4" style="height: 200px; object-fit: cover;" alt="Cosplay 2">
                        </div>
                        <div class="hero-img-box shadow-lg">
                            <img src="https://images.unsplash.com/photo-1578632767115-351597cf2477?w=400" class="img-fluid rounded-4" style="height: 200px; object-fit: cover;" alt="Cosplay 3">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KATALOG SECTION -->
<section id="katalog" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-uppercase text-gradient fw-bold tracking-widest small">— KOLEKSI PILIHAN —</span>
            <h2 class="title-fantasy display-5 mt-2">KATALOG <span class="text-gradient">KOSTUM</span></h2>
            <p class="text-secondary">Ratusan kostum premium siap disewa untuk berbagai karakter favoritmu</p>
            
            <!-- FILTER KOTA & SEARCH -->
            <form method="GET" action="#katalog" class="row justify-content-center g-2 mt-4">
                <div class="col-md-4">
                    <select name="kota" class="form-select py-2" onchange="this.form.submit()">
                        <option value="">📍 Semua Lokasi Kota</option>
                        <option value="Jakarta" <?= $kota_filter == 'Jakarta' ? 'selected' : '' ?>>Jakarta</option>
                        <option value="Bandung" <?= $kota_filter == 'Bandung' ? 'selected' : '' ?>>Bandung</option>
                        <option value="Yogyakarta" <?= $kota_filter == 'Yogyakarta' ? 'selected' : '' ?>>Yogyakarta</option>
                        <option value="Surabaya" <?= $kota_filter == 'Surabaya' ? 'selected' : '' ?>>Surabaya</option>
                    </select>
                </div>
            </form>

            <!-- FILTER KATEGORI -->
            <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
                <a href="?#katalog" class="filter-pill <?= empty($kategori_filter) ? 'active' : '' ?>">Semua</a>
                <a href="?kategori=Anime#katalog" class="filter-pill <?= $kategori_filter == 'Anime' ? 'active' : '' ?>">Anime</a>
                <a href="?kategori=Fantasy#katalog" class="filter-pill <?= $kategori_filter == 'Fantasy' ? 'active' : '' ?>">Fantasy</a>
                <a href="?kategori=Game#katalog" class="filter-pill <?= $kategori_filter == 'Game' ? 'active' : '' ?>">Game</a>
            </div>
        </div>

        <!-- GALLERY GRID -->
        <div class="row g-4">
            <?php if(mysqli_num_rows($query) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($query)): ?>
                    <div class="col-md-4">
                        <div class="glass-card p-3 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="position-relative mb-3">
                                    <img src="https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=500" class="img-fluid rounded-4 w-100" style="height: 280px; object-fit: cover;" alt="Kostum">
                                    <span class="badge position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill bg-success bg-opacity-75">
                                        • <?= ucfirst($row['status']) ?>
                                    </span>
                                </div>
                                <span class="badge bg-secondary bg-opacity-25 text-info mb-2"><?= $row['nama_kategori'] ?? 'Cosplay' ?></span>
                                <h4 class="fw-bold mb-1"><?= $row['nama_kostum'] ?></h4>
                                <p class="text-secondary small mb-3">
                                    <i class="bi bi-geo-alt text-danger me-1"></i> Studio | Ukuran: <?= $row['ukuran'] ?>
                                </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top border-secondary border-opacity-25">
                                <div>
                                    <span class="fs-4 fw-bold text-gradient">Rp <?= number_format($row['harga_sewa_per_hari'], 0, ',', '.') ?></span>
                                    <small class="text-secondary">/hari</small>
                                </div>
                                <a href="booking.php?id=<?= $row['id'] ?>" class="btn btn-neon py-2 px-4">Sewa</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5 text-secondary">
                    <i class="bi bi-search fs-1 d-block mb-2"></i>
                    <p>Kostum tidak ditemukan untuk filter ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const themeToggleBtn = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;

        const savedTheme = localStorage.getItem('cosplayrent_theme') || 'dark';
        setTheme(savedTheme);

        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });

        function setTheme(theme) {
            htmlElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('cosplayrent_theme', theme);
            
            if (theme === 'light') {
                themeToggleBtn.innerHTML = '<i class="bi bi-moon-stars-fill" style="color: #ff9800;"></i>';
                themeToggleBtn.style.backgroundColor = '#ffffff';
                themeToggleBtn.style.borderColor = 'rgba(176, 0, 255, 0.2)';
            } else {
                themeToggleBtn.innerHTML = '<i class="bi bi-sun-fill" style="color: #ffc107;"></i>';
                themeToggleBtn.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
                themeToggleBtn.style.borderColor = 'rgba(255, 255, 255, 0.1)';
            }
        }
    });
</script>
</body>
</html>