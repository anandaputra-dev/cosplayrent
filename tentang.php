
<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami | COSPLAYRENT</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #ffffff;
            color: #251536;
        }

        /* ================= NAVBAR ================= */

        .header {
            width: 100%;
            background: #ffffff;
            padding: 18px 0;
            border-bottom: 1px solid #eee8f5;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header .container {
            max-width: 1200px;
            margin: auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
        }

        .logo-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #a855f7, #7c3aed);
            color: white;
            border-radius: 12px;
            font-size: 25px;
            font-weight: 800;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .logo-text strong {
            color: #251536;
            font-size: 18px;
            font-weight: 800;
        }

        .logo-text strong span {
            color: #9333ea;
        }

        .logo-text small {
            color: #93869f;
            font-size: 8px;
            letter-spacing: 1.5px;
            margin-top: 3px;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .nav a {
            color: #51445f;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: .3s;
        }

        .nav a:hover,
        .nav a.active {
            color: #9333ea;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn-login {
            color: #51445f;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: .3s;
        }

        .btn-login:hover {
            color: #9333ea;
        }

        .btn-register {
            background: #9333ea;
            color: white;
            padding: 11px 22px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: .3s;
        }

        .btn-register:hover {
            background: #7e22ce;
            color: white;
        }

        .menu-toggle {
            display: none;
            border: none;
            background: transparent;
            color: #251536;
            font-size: 25px;
            cursor: pointer;
        }

        /* ================= HERO ================= */

        .hero {
            min-height: 430px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 80px 20px;
            background:
                linear-gradient(rgba(20, 10, 35, .55), rgba(20, 10, 35, .65)),
                url('assets/img/profilweb1.jpg') center/cover no-repeat;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 18px;
        }

        .hero h1 span {
            color: #d8a7ff;
        }

        .hero p {
            max-width: 700px;
            margin: auto;
            color: #f1eafa;
            font-size: 15px;
            line-height: 1.9;
        }

        /* ================= ABOUT ================= */

        .about-section {
            padding: 90px 0;
            background: #ffffff;
        }

        .about-image {
            width: 100%;
            height: 420px;
            object-fit: cover;
            border-radius: 22px;
            box-shadow: 0 15px 40px rgba(80, 40, 120, .12);
        }

        .section-label {
            color: #9333ea;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 12px;
            display: inline-block;
        }

        .section-title {
            font-size: 32px;
            font-weight: 800;
            color: #251536;
            margin-bottom: 20px;
        }

        .section-title span {
            color: #9333ea;
        }

        .about-text {
            color: #74677f;
            font-size: 14px;
            line-height: 2;
            margin-bottom: 15px;
        }

        /* ================= VISI MISI ================= */

        .vision-section {
            padding: 70px 0;
            background: #f8f5fc;
        }

        .info-card {
            height: 100%;
            padding: 35px;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #eee5f7;
            box-shadow: 0 8px 25px rgba(80, 40, 120, .05);
            transition: .3s;
        }

        .info-card:hover {
            transform: translateY(-6px);
            border-color: #c084fc;
            box-shadow: 0 12px 30px rgba(147, 51, 234, .10);
        }

        .info-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            background: #f3e8ff;
            color: #9333ea;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            margin-bottom: 22px;
        }

        .info-card h3 {
            color: #251536;
            font-size: 21px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .info-card p,
        .info-card li {
            color: #74677f;
            font-size: 13px;
            line-height: 1.9;
        }

        .info-card ul {
            padding-left: 18px;
            margin-bottom: 0;
        }

        /* ================= KEUNGGULAN ================= */

        .advantages-section {
            padding: 85px 0;
            background: #ffffff;
        }

        .advantage-card {
            text-align: center;
            padding: 30px 20px;
            height: 100%;
        }

        .advantage-icon {
            width: 65px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            background: #f3e8ff;
            color: #9333ea;
            border-radius: 18px;
            font-size: 28px;
        }

        .advantage-card h4 {
            color: #251536;
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .advantage-card p {
            color: #74677f;
            font-size: 13px;
            line-height: 1.8;
        }

        /* ================= CTA ================= */

        .cta-section {
            padding: 65px 20px;
            text-align: center;
            background: linear-gradient(135deg, #9333ea, #7e22ce);
        }

        .cta-section h2 {
            color: #ffffff;
            font-size: 30px;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .cta-section p {
            color: #f3e8ff;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .cta-button {
            display: inline-block;
            background: #ffffff;
            color: #7e22ce;
            padding: 13px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            transition: .3s;
        }

        .cta-button:hover {
            background: #f3e8ff;
            color: #581c87;
            transform: translateY(-3px);
        }

        /* ================= FOOTER ================= */

        footer {
            background: #f8f5fc;
            padding: 35px 0;
            text-align: center;
            border-top: 1px solid #eee5f7;
        }

        footer p {
            color: #93869f;
            font-size: 12px;
            margin: 0;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 991px) {
            .header .container {
                flex-wrap: wrap;
            }

            .menu-toggle {
                display: block;
            }

            .nav {
                display: none;
                width: 100%;
                flex-direction: column;
                align-items: flex-start;
                gap: 18px;
                padding: 20px 0;
            }

            .nav.show {
                display: flex;
            }

            .nav-actions {
                display: none;
                width: 100%;
                padding: 0 0 15px;
            }

            .nav-actions.show {
                display: flex;
            }
        }

        @media (max-width: 768px) {
            .hero {
                min-height: 350px;
            }

            .hero h1 {
                font-size: 34px;
            }

            .section-title {
                font-size: 26px;
            }

            .about-image {
                height: 300px;
                margin-bottom: 30px;
            }

            .cta-section h2 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<header class="header">
    <div class="container">

        <a href="index.php" class="logo">
            <span class="logo-icon">C</span>
            <span class="logo-text">
                <strong>COSPLAY<span>RENT</span></strong>
                <small>KARAKTERMU, MOMENMU</small>
            </span>
        </a>

        <nav class="nav" id="nav">
            <a href="index.php#beranda">Beranda</a>
            <a href="index.php#katalog">Katalog Kostum</a>
            <a href="index.php#cara-sewa">Cara Sewa</a>
            <a class="active" href="tentang.php">Tentang Kami</a>
        </nav>

        <div class="nav-actions">
            <a href="login.php" class="btn-login">Masuk</a>
            <a href="register.php" class="btn-register">Daftar ↗</a>
        </div>

        <button class="menu-toggle" id="menuToggle" type="button"
            aria-label="Buka menu navigasi" aria-expanded="false">
            ☰
        </button>

    </div>
</header>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <h1>Tentang <span>COSPLAYRENT</span></h1>
        <p>
            Kenali lebih dekat COSPLAYRENT, platform penyewaan kostum cosplay
            yang membantu kamu mewujudkan karakter favorit dengan mudah,
            praktis, dan menyenangkan.
        </p>
    </div>
</section>

<!-- TENTANG KAMI -->
<section class="about-section">
    <div class="container">
        <div class="row align-items-center g-5">

            <div class="col-lg-6">
                <img
                    src="assets/img/profilweb2.jpg"
                    alt="Tentang COSPLAYRENT"
                    class="about-image"
                >
            </div>

            <div class="col-lg-6">
                <span class="section-label">SIAPA KAMI?</span>

                <h2 class="section-title">
                    Wujudkan Karakter <span>Favoritmu</span>
                </h2>

                <p class="about-text">
                    COSPLAYRENT merupakan platform penyewaan kostum cosplay
                    berbasis website yang menyediakan berbagai pilihan
                    kostum dari karakter anime, game, superhero, fantasy,
                    dan berbagai kategori lainnya.
                </p>

                <p class="about-text">
                    Website ini dirancang untuk memudahkan pengguna dalam
                    mencari kostum, melihat informasi produk, mengetahui
                    harga sewa, serta menemukan kostum yang sesuai dengan
                    kebutuhan mereka.
                </p>

                <p class="about-text">
                    Dengan tampilan yang sederhana dan mudah digunakan,
                    COSPLAYRENT diharapkan dapat memberikan pengalaman
                    penyewaan kostum yang lebih praktis dan menyenangkan.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- VISI MISI -->
<section class="vision-section">
    <div class="container">

        <div class="text-center mb-5">
            <span class="section-label">TUJUAN KAMI</span>
            <h2 class="section-title">Visi dan <span>Misi</span></h2>
        </div>

        <div class="row g-4">

            <div class="col-lg-6">
                <div class="info-card">
                    <div class="info-icon">✦</div>
                    <h3>Visi</h3>
                    <p>
                        Menjadi platform penyewaan kostum cosplay berbasis
                        website yang praktis, informatif, dan dapat
                        memudahkan masyarakat dalam menemukan kostum
                        karakter favorit mereka.
                    </p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="info-card">
                    <div class="info-icon">✧</div>
                    <h3>Misi</h3>
                    <ul>
                        <li>Menyediakan informasi koleksi kostum secara lengkap.</li>
                        <li>Mempermudah pengguna dalam mencari kostum.</li>
                        <li>Memberikan informasi harga dan ketersediaan kostum.</li>
                        <li>Mengembangkan layanan penyewaan yang mudah digunakan.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- KEUNGGULAN -->
<section class="advantages-section">
    <div class="container">

        <div class="text-center mb-5">
            <span class="section-label">KENAPA COSPLAYRENT?</span>
            <h2 class="section-title">
                Keunggulan <span>Kami</span>
            </h2>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="advantage-card">
                    <div class="advantage-icon">✦</div>
                    <h4>Koleksi Beragam</h4>
                    <p>
                        Menyediakan berbagai pilihan kostum dari beragam
                        karakter dan kategori.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="advantage-card">
                    <div class="advantage-icon">⌕</div>
                    <h4>Mudah Dicari</h4>
                    <p>
                        Pengguna dapat mencari dan menyaring kostum
                        berdasarkan kategori maupun kota.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="advantage-card">
                    <div class="advantage-icon">♡</div>
                    <h4>Praktis Digunakan</h4>
                    <p>
                        Tampilan website dirancang agar mudah dipahami
                        dan digunakan oleh pengguna.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Siap Menemukan Karakter Favoritmu?</h2>

        <p>
            Jelajahi berbagai koleksi kostum cosplay dan temukan
            pilihan yang sesuai dengan keinginanmu.
        </p>

        <a href="gallery.php" class="cta-button">
            Jelajahi Katalog Kostum ↗
        </a>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p>
            &copy; <?= date('Y') ?> COSPLAYRENT.
            Semua Hak Dilindungi.
        </p>
    </div>
</footer>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const nav = document.getElementById('nav');
    const navActions = document.querySelector('.nav-actions');

    menuToggle.addEventListener('click', function () {
        nav.classList.toggle('show');
        navActions.classList.toggle('show');

        const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', !isExpanded);
    });
</script>

</body>
</html>