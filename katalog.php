<?php
require_once 'koneksi.php';

// ======================================================
// AMBIL DATA FILTER
// ======================================================
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$kota_filter     = isset($_GET['kota']) ? $_GET['kota'] : '';


// ======================================================
// QUERY DASAR
// ======================================================
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
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CosplayRent - Katalog Kostum</title>

    <!-- BOOTSTRAP -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- GOOGLE FONT -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>

        /* =====================================================
           ROOT
        ===================================================== */

        :root {

            --bg-main: #070510;

            --bg-card: #100a22;

            --bg-card-hover: #130c29;

            --purple: #b000ff;

            --purple-light: #c12cff;

            --purple-dark: #7900ad;

            --cyan: #00d4ff;

            --blue: #168cff;

            --white: #ffffff;

            --text: #eeeaff;

            --muted: #89809f;

            --muted-dark: #5b536d;

            --border: #30205a;

            --green: #00e995;
        }


        /* =====================================================
           GLOBAL
        ===================================================== */

        * {
            box-sizing: border-box;
        }


        html {
            scroll-behavior: smooth;
        }


        body {

            margin: 0;

            padding: 0;

            min-height: 100vh;

            background:

                radial-gradient(
                    circle at 50% 0%,
                    rgba(125, 0, 190, 0.08),
                    transparent 35%
                ),

                var(--bg-main);

            color: var(--white);

            font-family: 'Inter', sans-serif;

        }


        /* =====================================================
           NAVBAR
        ===================================================== */

        .navbar-custom {

            height: 86px;

            background: rgba(7, 5, 16, 0.97);

            border-bottom: 1px solid #211733;

        }


        .navbar-custom .container {

            max-width: 1415px;

            height: 100%;

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        /* =====================================================
           LOGO
        ===================================================== */

        .navbar-brand {

            margin: 0;

            font-size: 25px !important;

            font-weight: 800 !important;

            letter-spacing: -1px;

            text-decoration: none;

        }


        .navbar-brand span:first-child {

            color: var(--cyan) !important;

        }


        .navbar-brand span:last-child {

            color: var(--purple) !important;

        }


        /* =====================================================
           NAVBAR BUTTON
        ===================================================== */

        .navbar-custom .d-flex {

            gap: 9px;

        }


        .navbar-custom .btn-outline-light {

            height: 41px;

            padding: 0 15px;

            display: flex;

            align-items: center;

            justify-content: center;

            border: 1px solid #d9d5e2 !important;

            border-radius: 6px;

            background: transparent !important;

            color: white !important;

            font-size: 15px;

            font-weight: 500;

            transition: .25s ease;

        }


        .navbar-custom .btn-outline-light:hover {

            background: white !important;

            color: #080513 !important;

        }


        /* =====================================================
           BUTTON SEWA SEKARANG
        ===================================================== */

        .btn-purple {

            height: 41px;

            padding: 0 18px;

            display: flex;

            align-items: center;

            justify-content: center;

            border: none !important;

            border-radius: 7px;

            background:

                linear-gradient(
                    135deg,
                    #c000ff,
                    #9700ed
                ) !important;

            color: white !important;

            font-size: 15px;

            font-weight: 600;

            box-shadow:

                0 5px 18px
                rgba(176, 0, 255, .18);

            transition: .25s ease;

        }


        .btn-purple:hover {

            transform: translateY(-1px);

            background:

                linear-gradient(
                    135deg,
                    #d42cff,
                    #a000ff
                ) !important;

            box-shadow:

                0 8px 25px
                rgba(176, 0, 255, .30);

        }


        /* =====================================================
           MAIN
        ===================================================== */

        .catalog-container {

            max-width: 1415px;

            margin: 0 auto;

            padding:

                58px 0 70px;

        }


        /* =====================================================
           HEADER KATALOG
        ===================================================== */

        .catalog-header {

            text-align: center;

            margin-bottom: 30px;

        }


        .catalog-subtitle {

            margin: 0 0 9px;

            color: #756c88;

            font-size: 14px;

            font-weight: 400;

            letter-spacing: .3px;

        }


        .catalog-title {

            margin: 0;

            color: white;

            font-size: 36px;

            line-height: 1.2;

            font-weight: 800;

            letter-spacing: -1px;

        }


        .catalog-title span {

            color: var(--cyan);

        }


        /* =====================================================
           FILTER
        ===================================================== */

        .filter-form {

            max-width: 710px;

            margin: 0 auto 52px;

            display: flex;

            gap: 17px;

        }


        .filter-item {

            width: 50%;

        }


        .filter-select {

            width: 100%;

            height: 42px;

            padding: 0 40px 0 14px;

            border: 1px solid #4d4b56 !important;

            border-radius: 6px;

            background-color: #22262b !important;

            color: white !important;

            font-size: 15px;

            outline: none;

            box-shadow: none !important;

            cursor: pointer;

            transition: .25s ease;

        }


        .filter-select:hover {

            border-color: #746b84 !important;

        }


        .filter-select:focus {

            border-color: var(--purple) !important;

            box-shadow:

                0 0 0 2px
                rgba(176, 0, 255, .08) !important;

        }


        .filter-select option {

            background: #171122;

            color: white;

        }


        /* =====================================================
           GRID
        ===================================================== */

        .catalog-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 26px;

        }


        /* =====================================================
           CARD
        ===================================================== */

        .card-kostum {

            position: relative;

            overflow: hidden;

            min-height: 484px;

            border: 1px solid var(--border);

            border-radius: 13px;

            background: var(--bg-card);

            transition:

                transform .3s ease,

                border-color .3s ease,

                box-shadow .3s ease;

        }


        .card-kostum:hover {

            transform: translateY(-5px);

            border-color: #7036a8;

            background: var(--bg-card-hover);

            box-shadow:

                0 18px 40px
                rgba(0, 0, 0, .35);

        }


        /* =====================================================
           GAMBAR
        ===================================================== */

        .card-image-wrapper {

            position: relative;

            width: 100%;

            height: 273px;

            overflow: hidden;

        }


        .card-kostum .card-img-top {

            width: 100%;

            height: 273px !important;

            display: block;

            object-fit: cover;

            border: none;

            border-radius: 0 !important;

            background: #171126;

            transition: transform .45s ease;

        }


        .card-kostum:hover .card-img-top {

            transform: scale(1.025);

        }


        /* =====================================================
           STATUS
        ===================================================== */

        .badge-status {

            position: absolute;

            top: 12px;

            right: 12px;

            z-index: 10;

            padding: 8px 17px !important;

            border: none !important;

            border-radius: 25px !important;

            background: var(--green) !important;

            color: #001a10 !important;

            font-size: 12px;

            font-weight: 700;

            line-height: 1;

        }


        /* =====================================================
           CARD BODY
        ===================================================== */

        .card-kostum .card-body {

            min-height: 211px;

            padding: 20px 18px 17px !important;

            background: var(--bg-card) !important;

        }


        /* =====================================================
           KATEGORI
        ===================================================== */

        .badge-kategori {

            display: inline-block;

            margin-bottom: 9px;

            padding: 4px 10px !important;

            border: none !important;

            border-radius: 5px !important;

            background: #241554 !important;

            color: var(--cyan) !important;

            font-size: 12px !important;

            font-weight: 700;

        }


        /* =====================================================
           NAMA KOSTUM
        ===================================================== */

        .card-kostum .card-title {

            margin-bottom: 10px !important;

            color: white !important;

            font-size: 21px !important;

            line-height: 1.25;

            font-weight: 800 !important;

            letter-spacing: -.3px;

        }


        /* =====================================================
           DESKRIPSI
        ===================================================== */

        .card-kostum .text-muted {

            margin-bottom: 0;

            color: #5f5873 !important;

            font-size: 13px;

            line-height: 1.6;

        }


        /* =====================================================
           BAGIAN BAWAH CARD
        ===================================================== */

        .card-bottom {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-top: 39px;

        }


        /* =====================================================
           HARGA
        ===================================================== */

        .card-kostum .text-primary {

            color: #008cff !important;

            font-size: 21px !important;

            font-weight: 800 !important;

        }


        .price-per-day {

            color: #4c465c;

            font-size: 13px;

        }


        /* =====================================================
           BUTTON SEWA
        ===================================================== */

        .btn-neon {

            padding: 10px 17px !important;

            border: none !important;

            border-radius: 8px !important;

            background:

                linear-gradient(
                    135deg,
                    #c000ff,
                    #9700ed
                ) !important;

            color: white !important;

            font-size: 13px;

            font-weight: 600;

            box-shadow:

                0 5px 15px
                rgba(176, 0, 255, .15);

            transition: .25s ease;

        }


        .btn-neon:hover {

            transform: translateY(-2px);

            background:

                linear-gradient(
                    135deg,
                    #d22aff,
                    #a600ff
                ) !important;

            color: white !important;

            box-shadow:

                0 8px 20px
                rgba(176, 0, 255, .28);

        }


        /* =====================================================
           DATA KOSONG
        ===================================================== */

        .empty-data {

            grid-column: 1 / -1;

            padding: 70px 20px;

            text-align: center;

            color: #716982;

        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1200px) {

            .navbar-custom .container {

                max-width: 90%;

            }

            .catalog-container {

                max-width: 90%;

            }

        }


        @media (max-width: 900px) {

            .catalog-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }

        }


        @media (max-width: 767px) {

            .navbar-custom {

                height: 74px;

            }


            .navbar-custom .container {

                max-width: 92%;

            }


            .navbar-brand {

                font-size: 21px !important;

            }


            .catalog-container {

                max-width: 92%;

                padding-top: 40px;

            }


            .catalog-title {

                font-size: 30px;

            }


            .filter-form {

                flex-direction: column;

                gap: 12px;

                margin-bottom: 35px;

            }


            .filter-item {

                width: 100%;

            }


            .catalog-grid {

                grid-template-columns: 1fr;

            }


            .navbar-custom .btn-outline-light {

                display: none;

            }

        }


        @media (max-width: 480px) {

            .navbar-brand {

                font-size: 19px !important;

            }


            .btn-purple {

                padding: 0 13px;

                font-size: 12px;

            }


            .catalog-title {

                font-size: 27px;

            }


            .card-kostum .card-title {

                font-size: 19px !important;

            }

        }

    </style>

</head>


<body>


<!-- ======================================================
     NAVBAR
====================================================== -->

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">

    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="#"
        >
            <span>COSPLAY</span><span>RENT</span>
        </a>


        <div class="d-flex">

            <a
                href="dashboard.php"
                class="btn btn-outline-light"
            >
                Dashboard
            </a>


            <a
                href="dashboard.php"
                class="btn btn-purple"
            >
                Sewa Sekarang
            </a>

        </div>

    </div>

</nav>


<!-- ======================================================
     MAIN CATALOG
====================================================== -->

<main class="catalog-container">


    <!-- HEADER -->

    <div class="catalog-header">

        <p class="catalog-subtitle">
            — KOLEKSI PILIHAN —
        </p>


        <h1 class="catalog-title">
            KATALOG <span>KOSTUM</span>
        </h1>

    </div>


    <!-- ==================================================
         FILTER
    ================================================== -->

    <form
        method="GET"
        action=""
        class="filter-form"
    >


        <!-- KOTA -->

        <div class="filter-item">

            <select
                name="kota"
                class="filter-select"
                onchange="this.form.submit()"
            >

                <option value="">
                    -- Semua Kota --
                </option>

                <option
                    value="Jakarta"
                    <?= $kota_filter == 'Jakarta' ? 'selected' : '' ?>
                >
                    Jakarta
                </option>

                <option
                    value="Bandung"
                    <?= $kota_filter == 'Bandung' ? 'selected' : '' ?>
                >
                    Bandung
                </option>

                <option
                    value="Yogyakarta"
                    <?= $kota_filter == 'Yogyakarta' ? 'selected' : '' ?>
                >
                    Yogyakarta
                </option>

                <option
                    value="Surabaya"
                    <?= $kota_filter == 'Surabaya' ? 'selected' : '' ?>
                >
                    Surabaya
                </option>

                <option
                    value="Malang"
                    <?= $kota_filter == 'Malang' ? 'selected' : '' ?>
                >
                    Malang
                </option>

                <option
                    value="Kediri"
                    <?= $kota_filter == 'Kediri' ? 'selected' : '' ?>
                >
                    Kediri
                </option>

                <option
                    value="Batu"
                    <?= $kota_filter == 'Batu' ? 'selected' : '' ?>
                >
                    Batu
                </option>

                <option
                    value="Solo"
                    <?= $kota_filter == 'Solo' ? 'selected' : '' ?>
                >
                    Solo
                </option>

            </select>

        </div>


        <!-- KATEGORI -->

        <div class="filter-item">

            <select
                name="kategori"
                class="filter-select"
                onchange="this.form.submit()"
            >

                <option value="">
                    -- Semua Kategori --
                </option>

                <option
                    value="Anime"
                    <?= $kategori_filter == 'Anime' ? 'selected' : '' ?>
                >
                    Anime
                </option>

                <option
                    value="Fantasy"
                    <?= $kategori_filter == 'Fantasy' ? 'selected' : '' ?>
                >
                    Fantasy
                </option>

                <option
                    value="Game"
                    <?= $kategori_filter == 'Game' ? 'selected' : '' ?>
                >
                    Game
                </option>

            </select>

        </div>

    </form>


    <!-- ==================================================
         GALLERY
    ================================================== -->

    <div class="catalog-grid">


        <?php if(mysqli_num_rows($query) > 0): ?>


            <?php while($row = mysqli_fetch_assoc($query)): ?>


                <!-- CARD -->

                <div class="card-kostum">


                    <!-- GAMBAR -->

                    <div class="card-image-wrapper">

                        <span class="badge badge-status">

                            <?= ucfirst($row['status']) ?>

                        </span>


                        <img
                            src="https://images.unsplash.com/photo-1534447677768-be436bb09401?w=800"
                            class="card-img-top"
                            alt="<?= htmlspecialchars($row['nama_kostum']) ?>"
                        >

                    </div>


                    <!-- CARD BODY -->

                    <div class="card-body">


                        <!-- KATEGORI -->

                        <span class="badge badge-kategori">

                            <?= $row['nama_kategori'] ?? 'Umum' ?>

                        </span>


                        <!-- NAMA -->

                        <h5 class="card-title">

                            <?= htmlspecialchars($row['nama_kostum']) ?>

                        </h5>


                        <!-- INFO -->

                        <p class="text-muted small">

                            Ukuran:
                            <?= htmlspecialchars($row['ukuran']) ?>

                            |

                            Character:
                            <?= htmlspecialchars($row['nama_karakter']) ?>

                        </p>


                        <!-- BOTTOM -->

                        <div class="card-bottom">


                            <!-- HARGA -->

                            <div>

                                <span class="text-primary">

                                    Rp
                                    <?= number_format(
                                        $row['harga_sewa_per_hari'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </span>


                                <span class="price-per-day">
                                    /hari
                                </span>

                            </div>


                            <!-- BUTTON -->

                            <a
                                href="booking.php?id=<?= $row['id'] ?>"
                                class="btn btn-neon"
                            >
                                Sewa
                            </a>

                        </div>


                    </div>

                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <div class="empty-data">

                <p>
                    Tidak ada kostum yang cocok
                    dengan filter yang dipilih.
                </p>

            </div>


        <?php endif; ?>


    </div>


</main>


</body>

</html>