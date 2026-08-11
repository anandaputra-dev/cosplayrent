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
    <!-- Google Fonts untuk Header Fantasi -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --bg-dark: #070510;
            --card-bg: rgba(20, 14, 40, 0.7);
            --border-purple: rgba(176, 0, 255, 0.25);
            --neon-purple: #b000ff;
            --neon-cyan: #00d4ff;
            --font-fantasy: 'Cinzel', serif;
            --font-main: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: #ffffff;
            font-family: var(--font-main);
            overflow-x: hidden;
            /* Pattern Grid Transparan Sesuai Gambar Figma */
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* NAVBAR */
        .navbar-custom {
            background: rgba(7, 5, 16, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .nav-link {
            color: #a0a0b8 !important;
            font-weight: 500;
            margin: 0 10px;
            transition: 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: #ffffff !important;
            background: rgba(176, 0, 255, 0.15);
            border-radius: 8px;
        }

        /* GRADIENT TEXT & HEADING */
        .title-fantasy {
            font-family: var(--font-fantasy);
            font-weight: 900;
            letter-spacing: 2px;
            line-height: 1.1;
        }
        .text-gradient {
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* BUTTONS */
        .btn-neon {
            background: linear-gradient(90deg, #b000ff, #7900ff);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            padding: 12px 28px;
            box-shadow: 0 0 20px rgba(176, 0, 255, 0.4);
            transition: all 0.3s ease;
        }
        .btn-neon:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(176, 0, 255, 0.7);
            color: #fff;
        }
        .btn-outline-custom {
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 12px;
            padding: 12px 28px;
            backdrop-filter: blur(5px);
        }

        /* CARD STYLING */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-purple);
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            border-color: var(--neon-cyan);
            transform: translateY(-8px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.15);
        }

        /* BADGES */
        .badge-pill-custom {
            background: rgba(176, 0, 255, 0.2);
            border: 1px solid var(--neon-purple);
            color: #e088ff;
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }
        .badge-available {
            background: rgba(0, 255, 136, 0.15);
            border: 1px solid #00ff88;
            color: #00ff88;
        }

        /* IMAGE FLOATING OVERLAY (HERO SECTION) */
        .hero-img-box {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid var(--border-purple);
        }
        .floating-stat-box {
            position: absolute;
            bottom: 20px;
            left: -20px;
            background: rgba(15, 10, 30, 0.9);
            border: 1px solid var(--neon-purple);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 15px 25px;
            z-index: 10;
        }

        /* CATEGORY FILTER PILLS */
        .filter-pill {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #8f8fa8;
            border-radius: 30px;
            padding: 8px 24px;
            text-decoration: none;
            transition: 0.3s;
        }
        .filter-pill.active, .filter-pill:hover {
            background: var(--neon-purple);
            color: #fff;
            border-color: var(--neon-purple);
            box-shadow: 0 0 15px rgba(176, 0, 255, 0.5);
        }
    </style>
</head>
<body>

<!-- NAVBAR PRESISI FIGMA -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="#">
            <div class="bg-gradient p-2 rounded-3" style="background: linear-gradient(135deg, #00d4ff, #b000ff);">
                <i class="bi bi-mask text-white"></i>
            </div>
            <span class="fs-4 tracking-wider">COSPLAY<span style="color:var(--neon-purple)">RENT</span></span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active px-3" href="#beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#katalog">Katalog</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#cara-sewa">Cara Sewa</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#event">Event</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#kontak">Kontak</a></li>
            </ul>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-secondary rounded-circle" id="themeToggle" style="width: 42px; height: 42px;">
                <i class="bi bi-sun-fill text-warning"></i>
            </button>
            <a href="auth.php" class="btn btn-neon">Sewa Sekarang</a>
        </div>
    </div>
</nav>

<!-- HERO SECTION (FIGMA MATCH) -->
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

            <!-- HERO GALLERY STAGGERED (Sesuai Desain Figma) -->
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

<!-- KATALOG SECTION & FILTER KOTA -->
<section id="katalog" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-uppercase text-gradient fw-bold tracking-widest small">— KOLEKSI PILIHAN —</span>
            <h2 class="title-fantasy display-5 mt-2">KATALOG <span class="text-gradient">KOSTUM</span></h2>
            <p class="text-secondary">Ratusan kostum premium siap disewa untuk berbagai karakter favoritmu</p>
            
            <!-- FILTER KOTA & SEARCH -->
            <form method="GET" action="#katalog" class="row justify-content-center g-2 mt-4">
                <div class="col-md-3">
                    <select name="kota" class="form-select glass-card text-white py-2" onchange="this.form.submit()">
                        <option value="" class="bg-dark">📍 Semua Lokasi Kota</option>
                        <option value="Jakarta" class="bg-dark" <?= $kota_filter == 'Jakarta' ? 'selected' : '' ?>>Jakarta</option>
                        <option value="Bandung" class="bg-dark" <?= $kota_filter == 'Bandung' ? 'selected' : '' ?>>Bandung</option>
                        <option value="Yogyakarta" class="bg-dark" <?= $kota_filter == 'Yogyakarta' ? 'selected' : '' ?>>Yogyakarta</option>
                        <option value="Surabaya" class="bg-dark" <?= $kota_filter == 'Surabaya' ? 'selected' : '' ?>>Surabaya</option>
                    </select>
                </div>
            </form>

            <!-- FILTER KATEGORI BUTTONS -->
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
                                    <span class="badge badge-available position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill">
                                        • <?= ucfirst($row['status']) ?>
                                    </span>
                                </div>
                                <span class="badge bg-secondary bg-opacity-25 text-info mb-2"><?= $row['nama_kategori'] ?? 'Cosplay' ?></span>
                                <h4 class="fw-bold mb-1"><?= $row['nama_kostum'] ?></h4>
                                <p class="text-secondary small mb-3">
                                    <i class="bi bi-geo-alt text-danger me-1"></i> Jakarta Studio | Ukuran: <?= $row['ukuran'] ?>
                                </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top border-secondary border-opacity-25">
                                <div>
                                    <span class="fs-4 fw-bold text-gradient">Rp <?= number_format($row['harga_sewa_per_hari'], 0, ',', '.') ?></span>
                                    <small class="text-secondary">/hari</small>
                                </div>
                                <a href="auth.php" class="btn btn-neon py-2 px-4">Sewa</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5 text-secondary">
                    <i class="bi bi-search fs-1"></i>
                    <p class="mt-2">Kostum tidak ditemukan untuk filter ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- JADWAL EVENT COSPLAY -->
<section id="event" class="py-5 my-4">
    <div class="container">
        <div class="glass-card p-5">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <span class="text-uppercase text-gradient fw-bold">JADWAL EVENT TERKINI</span>
                    <h2 class="title-fantasy mt-2">SIAP TAMPIL DI <span class="text-gradient">EVENT COSPLAY?</span></h2>
                    <p class="text-secondary">Booking kostum favoritmu lebih awal sebelum kehabisan slot pada tanggal event mendatang.</p>
                </div>
                <div class="col-md-7">
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="text-center p-2 rounded-3 bg-primary bg-opacity-25 text-primary" style="min-width: 60px;">
                                    <span class="fw-bold fs-4 d-block">25</span>
                                    <small>MEI</small>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Comic Frontier (Comifuro) 18</h5>
                                    <small class="text-secondary">📍 ICE BSD, Tangerang</small>
                                </div>
                            </div>
                            <a href="#katalog" class="btn btn-outline-custom btn-sm">Sewa Kostum</a>
                        </div>
                        <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="text-center p-2 rounded-3 bg-purple bg-opacity-25 text-purple" style="min-width: 60px; background: rgba(176,0,255,0.2); color:#b000ff;">
                                    <span class="fw-bold fs-4 d-block">15</span>
                                    <small>JUNI</small>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Indonesia Anime Con (INACON)</h5>
                                    <small class="text-secondary">📍 JCC Senayan, Jakarta</small>
                                </div>
                            </div>
                            <a href="#katalog" class="btn btn-outline-custom btn-sm">Sewa Kostum</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JS SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Theme Toggle Functionality (Light/Dark Mode)
    const themeToggleBtn = document.getElementById('themeToggle');
    themeToggleBtn.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        themeToggleBtn.innerHTML = newTheme === 'dark' ? '<i class="bi bi-sun-fill text-warning"></i>' : '<i class="bi bi-moon-stars-fill text-dark"></i>';
    });
</script>
</body>
</html>