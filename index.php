<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php';

$kategori_filter = isset($_GET['kategori'])
    ? trim($_GET['kategori'])
    : '';

$kota_filter = isset($_GET['kota'])
    ? trim($_GET['kota'])
    : '';


/* KATEGORI */

$sqlKategori = "
    SELECT id, nama_kategori
    FROM kategori
    ORDER BY nama_kategori ASC
";

$queryKategori = mysqli_query($conn, $sqlKategori);

if (!$queryKategori) {
    die("Query kategori gagal: " . mysqli_error($conn));
}


/* KOSTUM */

$sql = "
    SELECT
        k.id,
        k.nama_kostum,
        k.nama_karakter,
        k.nama_serial,
        k.kategori_id,
        k.ukuran,
        k.deskripsi,
        k.harga_sewa_per_hari,
        k.status,

        cat.nama_kategori,

        (
            SELECT fk.foto_url
            FROM foto_kostum fk
            WHERE fk.kostum_id = k.id
            ORDER BY fk.foto_utama DESC, fk.id ASC
            LIMIT 1
        ) AS foto_url

    FROM kostum k

    LEFT JOIN kategori cat
        ON k.kategori_id = cat.id

    WHERE 1 = 1
";


/* FILTER KATEGORI */

if ($kategori_filter !== '') {

    $kategori_safe = mysqli_real_escape_string(
        $conn,
        $kategori_filter
    );

    $sql .= "
        AND cat.nama_kategori = '$kategori_safe'
    ";
}


/* FILTER KOTA */

if ($kota_filter !== '') {

    $kota_safe = mysqli_real_escape_string(
        $conn,
        $kota_filter
    );

    $sql .= "
        AND k.deskripsi LIKE '%$kota_safe%'
    ";
}


/* URUTKAN */

$sql .= "
    ORDER BY k.id DESC
";


/* EKSEKUSI QUERY */

$query = mysqli_query($conn, $sql);


/* CEK ERROR */

if (!$query) {

    die(
        "Query kostum gagal: " .
        mysqli_error($conn)
    );

}


/* TOTAL KOSTUM */

$sqlTotal = "
    SELECT COUNT(*) AS total
    FROM kostum
";

$queryTotal = mysqli_query($conn, $sqlTotal);

$totalKostum = 0;

if ($queryTotal) {

    $dataTotal = mysqli_fetch_assoc($queryTotal);

    $totalKostum = (int) $dataTotal['total'];

}


/* TOTAL TERSEDIA */

$sqlTersedia = "
    SELECT COUNT(*) AS total
    FROM kostum
    WHERE status = 'tersedia'
";

$queryTersedia = mysqli_query($conn, $sqlTersedia);

$totalTersedia = 0;

if ($queryTersedia) {

    $dataTersedia = mysqli_fetch_assoc($queryTersedia);

    $totalTersedia = (int) $dataTersedia['total'];

}


/* SESSION */

$isLogin = isset($_SESSION['user_id']);

$namaUser = $_SESSION['nama'] ?? '';

$peranUser = $_SESSION['peran'] ?? '';

?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        CosplayRent - Wujudkan Karakter Impianmu
    </title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    >


    <!-- CSS CUSTOM -->
    <link
        rel="stylesheet"
        href="style.css"
    >


    <style>

        /*
        |--------------------------------------------------------------------------
        | LOGIN BUTTON
        |--------------------------------------------------------------------------
        */

        .btn-login-custom {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-height: 42px;

            padding: 8px 18px;

            border: 1px solid rgba(168, 85, 247, 0.35);

            border-radius: 12px;

            background: rgba(255, 255, 255, 0.75);

            color: #4c1d95;

            font-weight: 600;

            text-decoration: none;

            transition: all 0.25s ease;

            white-space: nowrap;
        }


        .btn-login-custom:hover {

            background: #f3e8ff;

            border-color: #a855f7;

            color: #7e22ce;

            transform: translateY(-2px);
        }


        [data-bs-theme="dark"] .btn-login-custom {

            background: rgba(255, 255, 255, 0.06);

            border-color: rgba(255, 255, 255, 0.14);

            color: #e9d5ff;
        }


        [data-bs-theme="dark"] .btn-login-custom:hover {

            background: rgba(168, 85, 247, 0.15);

            border-color: #a855f7;

            color: #ffffff;
        }


        /*
        |--------------------------------------------------------------------------
        | COSTUME CARD
        |--------------------------------------------------------------------------
        */

        .costume-card {

            overflow: hidden;

            transition:
                transform .25s ease,
                box-shadow .25s ease;
        }


        .costume-card:hover {

            transform: translateY(-6px);

            box-shadow:
                0 20px 45px rgba(0, 0, 0, .25);
        }


        .costume-image {

            width: 100%;

            height: 280px;

            object-fit: cover;

            display: block;
        }


        .costume-image-wrapper {

            position: relative;

            overflow: hidden;

            border-radius: 16px;
        }


        .costume-image-wrapper img {

            transition:
                transform .4s ease;
        }


        .costume-card:hover
        .costume-image-wrapper img {

            transform: scale(1.04);
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        .status-badge {

            position: absolute;

            top: 14px;

            right: 14px;

            padding: 7px 12px;

            border-radius: 50px;

            font-size: 12px;

            font-weight: 700;
        }


        .status-tersedia {

            background: rgba(25, 135, 84, .9);

            color: white;
        }


        .status-disewa {

            background: rgba(220, 53, 69, .9);

            color: white;
        }


        .status-maintenance {

            background: rgba(255, 193, 7, .9);

            color: #111;
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        .filter-pill {

            display: inline-block;

            padding: 8px 18px;

            border-radius: 50px;

            text-decoration: none;

            color: inherit;

            border: 1px solid rgba(168, 85, 247, .25);

            transition: .2s ease;
        }


        .filter-pill:hover,
        .filter-pill.active {

            background: linear-gradient(
                135deg,
                #06b6d4,
                #a855f7
            );

            color: white;

            border-color: transparent;
        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .empty-state {

            padding: 60px 20px;

            text-align: center;
        }


        @media (max-width: 991.98px) {

            .btn-login-custom {

                padding: 7px 14px;
            }

        }


        @media (max-width: 767px) {

            .costume-image {

                height: 240px;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="navbar navbar-expand-lg navbar-custom sticky-top py-3">

    <div class="container">


        <!-- LOGO -->

        <a
            class="navbar-brand d-flex align-items-center gap-2 fw-bold"
            href="index.php"
        >

            <div
                class="p-2 rounded-3"
                style="
                    background:
                    linear-gradient(
                        135deg,
                        var(--neon-cyan),
                        var(--neon-purple)
                    );
                "
            >

                <i class="bi bi-mask text-white"></i>

            </div>


            <span class="fs-4 tracking-wider">

                COSPLAY

                <span style="color:var(--neon-purple)">
                    RENT
                </span>

            </span>

        </a>


        <!-- TOGGLE MOBILE -->

        <button
            class="navbar-toggler border-0 text-white"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
        >

            <i class="bi bi-list fs-2"></i>

        </button>


        <!-- NAVIGATION -->

        <div
            class="collapse navbar-collapse justify-content-center"
            id="navbarNav"
        >

            <ul class="navbar-nav">

                <li class="nav-item">

                    <a
                        class="nav-link active px-3"
                        href="#beranda"
                    >
                        Beranda
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link px-3"
                        href="#katalog"
                    >
                        Katalog
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link px-3"
                        href="#cara-sewa"
                    >
                        Cara Sewa
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link px-3"
                        href="#event"
                    >
                        Event
                    </a>

                </li>

            </ul>

        </div>


        <!-- RIGHT NAV -->

        <div class="d-flex align-items-center gap-3">


            <!-- THEME -->

            <button
                class="btn rounded-circle d-flex align-items-center justify-content-center p-0"
                id="themeToggle"
                style="
                    width:42px;
                    height:42px;
                    transition:all .3s ease;
                "
            >

                <i class="bi bi-sun-fill text-warning"></i>

            </button>


            <!-- LOGIN -->

            <?php if ($isLogin): ?>

                <?php

                if ($peranUser === 'admin') {

                    $dashboardLink = 'admin/dashboard.php';

                } else {

                    $dashboardLink = 'dashboard.php';

                }

                ?>


                <a
                    href="<?= htmlspecialchars($dashboardLink) ?>"
                    class="btn btn-login-custom"
                >

                    <i class="bi bi-person-circle me-1"></i>

                    <?= htmlspecialchars($namaUser) ?>

                </a>


            <?php else: ?>


                <a
                    href="login.php"
                    class="btn btn-login-custom"
                >

                    <i class="bi bi-box-arrow-in-right me-1"></i>

                    Login

                </a>


            <?php endif; ?>


            <!-- KATALOG -->

            <a
                href="#katalog"
                class="btn btn-neon"
            >
                Sewa Sekarang
            </a>

        </div>

    </div>

</nav>



<!-- =========================================================
     HERO
========================================================= -->

<section
    id="beranda"
    class="py-5 my-4"
>

    <div class="container">

        <div class="row align-items-center g-5">


            <!-- HERO TEXT -->

            <div class="col-lg-6">


                <span
                    class="badge-pill-custom text-uppercase fw-bold mb-3 d-inline-block"
                >

                    • <?= $totalTersedia ?> Kostum Tersedia

                </span>


                <h1
                    class="title-fantasy display-3 mb-3"
                >

                    WUJUDKAN

                    <br>

                    <span class="text-gradient">
                        KARAKTER
                    </span>

                    <br>

                    IMPIANMU

                </h1>


                <p
                    class="text-secondary fs-5 mb-4"
                    style="max-width:500px;"
                >

                    Dari anime legendaris hingga ksatria fantasi —
                    kami menyediakan kostum cosplay premium berkualitas
                    tinggi untuk event, pemotretan, atau sekadar tampil keren.

                </p>


                <div class="d-flex gap-3 mb-5">

                    <a
                        href="#katalog"
                        class="btn btn-neon fs-6"
                    >

                        Jelajahi Katalog →

                    </a>


                    <a
                        href="#cara-sewa"
                        class="btn btn-outline-custom fs-6"
                    >

                        Cara Sewa

                    </a>

                </div>


                <!-- STATS -->

                <div
                    class="
                        row
                        pt-4
                        border-top
                        border-secondary
                        border-opacity-25
                    "
                >


                    <div class="col-4">

                        <h3
                            class="
                                fw-bold
                                text-gradient
                                mb-0
                            "
                        >

                            <?= $totalKostum ?>+

                        </h3>

                        <small class="text-secondary">
                            Kostum
                        </small>

                    </div>


                    <div class="col-4">

                        <h3
                            class="
                                fw-bold
                                text-gradient
                                mb-0
                            "
                        >

                            <?= $totalTersedia ?>

                        </h3>

                        <small class="text-secondary">
                            Tersedia
                        </small>

                    </div>


                    <div class="col-4">

                        <h3
                            class="
                                fw-bold
                                text-gradient
                                mb-0
                            "
                        >

                            5★

                        </h3>

                        <small class="text-secondary">
                            Rating
                        </small>

                    </div>


                </div>

            </div>



            <!-- HERO IMAGE -->

            <div class="col-lg-6">

                <div class="row g-3 position-relative">


                    <div class="col-7">

                        <div class="hero-img-box shadow-lg">

                            <img
                                src="https://images.unsplash.com/photo-1534447677768-be436bb09401?w=600"
                                class="img-fluid rounded-4"
                                style="
                                    height:420px;
                                    object-fit:cover;
                                "
                                alt="Cosplay"
                            >

                        </div>

                    </div>


                    <div class="col-5">


                        <div class="hero-img-box mb-3 shadow-lg">

                            <img
                                src="https://images.unsplash.com/photo-1563089145-599997674d42?w=400"
                                class="img-fluid rounded-4"
                                style="
                                    height:200px;
                                    object-fit:cover;
                                "
                                alt="Cosplay"
                            >

                        </div>


                        <div class="hero-img-box shadow-lg">

                            <img
                                src="https://images.unsplash.com/photo-1578632767115-351597cf2477?w=400"
                                class="img-fluid rounded-4"
                                style="
                                    height:200px;
                                    object-fit:cover;
                                "
                                alt="Cosplay"
                            >

                        </div>


                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     KATALOG / GALLERY
========================================================= -->

<section
    id="katalog"
    class="py-5"
>

    <div class="container">


        <!-- TITLE -->

        <div class="text-center mb-5">


            <span
                class="
                    text-uppercase
                    text-gradient
                    fw-bold
                    tracking-widest
                    small
                "
            >

                — KOLEKSI KOSTUM —

            </span>


            <h2
                class="
                    title-fantasy
                    display-5
                    mt-2
                "
            >

                KATALOG

                <span class="text-gradient">
                    KOSTUM
                </span>

            </h2>


            <p class="text-secondary">

                Semua data kostum diambil langsung
                dari database COSPLAYRENT.

            </p>


        </div>



        <!-- =====================================================
             FILTER
        ====================================================== -->

        <div class="mb-5">


            <!-- FILTER KOTA -->

            <form
                method="GET"
                action="index.php#katalog"
                class="row justify-content-center g-2"
            >

                <?php if ($kategori_filter !== ''): ?>

                    <input
                        type="hidden"
                        name="kategori"
                        value="<?= htmlspecialchars($kategori_filter) ?>"
                    >

                <?php endif; ?>


                <div class="col-md-4">

                    <select
                        name="kota"
                        class="form-select py-2"
                        onchange="this.form.submit()"
                    >

                        <option value="">
                            📍 Semua Lokasi Kota
                        </option>


                        <option
                            value="Jakarta"
                            <?= $kota_filter === 'Jakarta' ? 'selected' : '' ?>
                        >
                            Jakarta
                        </option>


                        <option
                            value="Bandung"
                            <?= $kota_filter === 'Bandung' ? 'selected' : '' ?>
                        >
                            Bandung
                        </option>


                        <option
                            value="Yogyakarta"
                            <?= $kota_filter === 'Yogyakarta' ? 'selected' : '' ?>
                        >
                            Yogyakarta
                        </option>


                        <option
                            value="Surabaya"
                            <?= $kota_filter === 'Surabaya' ? 'selected' : '' ?>
                        >
                            Surabaya
                        </option>

                    </select>

                </div>

            </form>



            <!-- FILTER KATEGORI DATABASE -->

            <div
                class="
                    d-flex
                    justify-content-center
                    gap-2
                    flex-wrap
                    mt-3
                "
            >


                <a
                    href="index.php#katalog"
                    class="
                        filter-pill
                        <?= $kategori_filter === '' ? 'active' : '' ?>
                    "
                >

                    Semua

                </a>


                <?php if ($queryKategori): ?>

                    <?php while ($kategori = mysqli_fetch_assoc($queryKategori)): ?>


                        <a
                            href="
                                ?kategori=<?= urlencode($kategori['nama_kategori']) ?>#katalog
                            "
                            class="
                                filter-pill
                                <?= $kategori_filter === $kategori['nama_kategori']
                                    ? 'active'
                                    : '' ?>
                            "
                        >

                            <?= htmlspecialchars(
                                $kategori['nama_kategori']
                            ) ?>

                        </a>


                    <?php endwhile; ?>

                <?php endif; ?>


            </div>

        </div>



        <!-- =====================================================
             GALLERY GRID
        ====================================================== -->

        <div class="row g-4">


            <?php if ($query && mysqli_num_rows($query) > 0): ?>


                <?php while ($row = mysqli_fetch_assoc($query)): ?>


                    <?php

                    /*
                    |--------------------------------------------------------------------------
                    | FOTO
                    |--------------------------------------------------------------------------
                    */

                    $gambar = !empty($row['foto_url'])
                        ? $row['foto_url']
                        : 'images/default.jpg';


                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    */

                    $status = strtolower(
                        $row['status'] ?? ''
                    );


                    if ($status === 'tersedia') {

                        $statusClass = 'status-tersedia';

                    } elseif ($status === 'disewa') {

                        $statusClass = 'status-disewa';

                    } else {

                        $statusClass = 'status-maintenance';

                    }

                    ?>


                    <!-- CARD -->

                    <div class="col-md-6 col-lg-4">


                        <div
                            class="
                                glass-card
                                costume-card
                                p-3
                                h-100
                                d-flex
                                flex-column
                                justify-content-between
                            "
                        >


                            <div>


                                <!-- FOTO -->

                                <div
                                    class="
                                        costume-image-wrapper
                                        position-relative
                                        mb-3
                                    "
                                >

                                    <img
                                        src="<?= htmlspecialchars($gambar) ?>"
                                        class="costume-image"
                                        alt="<?= htmlspecialchars(
                                            $row['nama_kostum']
                                        ) ?>"
                                        loading="lazy"
                                    >


                                    <!-- STATUS -->

                                    <span
                                        class="
                                            status-badge
                                            <?= $statusClass ?>
                                        "
                                    >

                                        •
                                        <?= htmlspecialchars(
                                            ucfirst($row['status'])
                                        ) ?>

                                    </span>


                                </div>



                                <!-- KATEGORI -->

                                <span
                                    class="
                                        badge
                                        bg-secondary
                                        bg-opacity-25
                                        text-info
                                        mb-2
                                    "
                                >

                                    <?= htmlspecialchars(
                                        $row['nama_kategori']
                                        ?? 'Cosplay'
                                    ) ?>

                                </span>



                                <!-- NAMA KOSTUM -->

                                <h4 class="fw-bold mb-1">

                                    <?= htmlspecialchars(
                                        $row['nama_kostum']
                                    ) ?>

                                </h4>



                                <!-- KARAKTER -->

                                <?php if (!empty($row['nama_karakter'])): ?>

                                    <p
                                        class="
                                            text-secondary
                                            small
                                            mb-1
                                        "
                                    >

                                        <i
                                            class="
                                                bi
                                                bi-person
                                                me-1
                                            "
                                        ></i>

                                        Karakter:

                                        <?= htmlspecialchars(
                                            $row['nama_karakter']
                                        ) ?>

                                    </p>

                                <?php endif; ?>



                                <!-- SERIAL -->

                                <?php if (!empty($row['nama_serial'])): ?>

                                    <p
                                        class="
                                            text-secondary
                                            small
                                            mb-1
                                        "
                                    >

                                        <i
                                            class="
                                                bi
                                                bi-film
                                                me-1
                                            "
                                        ></i>

                                        <?= htmlspecialchars(
                                            $row['nama_serial']
                                        ) ?>

                                    </p>

                                <?php endif; ?>



                                <!-- UKURAN -->

                                <p
                                    class="
                                        text-secondary
                                        small
                                        mb-3
                                    "
                                >

                                    <i
                                        class="
                                            bi
                                            bi-rulers
                                            me-1
                                        "
                                    ></i>

                                    Ukuran:

                                    <?= htmlspecialchars(
                                        $row['ukuran']
                                        ?? '-'
                                    ) ?>

                                </p>


                            </div>



                            <!-- HARGA + BUTTON -->

                            <div
                                class="
                                    d-flex
                                    align-items-center
                                    justify-content-between
                                    pt-3
                                    border-top
                                    border-secondary
                                    border-opacity-25
                                "
                            >


                                <div>

                                    <span
                                        class="
                                            fs-5
                                            fw-bold
                                            text-gradient
                                        "
                                    >

                                        Rp

                                        <?= number_format(
                                            (float) $row[
                                                'harga_sewa_per_hari'
                                            ],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </span>


                                    <small class="text-secondary">
                                        /hari
                                    </small>

                                </div>



                                <?php if ($status === 'tersedia'): ?>


                                    <a
                                        href="
                                            booking.php?id=<?= (int) $row['id'] ?>
                                        "
                                        class="
                                            btn
                                            btn-neon
                                            py-2
                                            px-4
                                        "
                                    >

                                        Sewa

                                    </a>


                                <?php else: ?>


                                    <button
                                        class="btn btn-secondary py-2 px-3"
                                        disabled
                                    >

                                        Tidak Tersedia

                                    </button>


                                <?php endif; ?>


                            </div>


                        </div>

                    </div>


                <?php endwhile; ?>


            <?php else: ?>


                <!-- EMPTY -->

                <div class="col-12">

                    <div class="empty-state text-secondary">

                        <i
                            class="
                                bi
                                bi-search
                                fs-1
                                d-block
                                mb-3
                            "
                        ></i>


                        <h5>
                            Kostum tidak ditemukan
                        </h5>


                        <p>
                            Belum ada kostum yang sesuai dengan filter.
                        </p>


                        <a
                            href="index.php#katalog"
                            class="btn btn-neon"
                        >

                            Lihat Semua Kostum

                        </a>

                    </div>

                </div>


            <?php endif; ?>


        </div>

    </div>

</section>



<!-- =========================================================
     CARA SEWA
========================================================= -->

<section
    id="cara-sewa"
    class="py-5"
>

    <div class="container">

        <div class="text-center mb-5">

            <span
                class="
                    text-uppercase
                    text-gradient
                    fw-bold
                    small
                "
            >
                — CARA SEWA —
            </span>


            <h2 class="title-fantasy display-5 mt-2">

                CARA

                <span class="text-gradient">
                    SEWA
                </span>

            </h2>

        </div>



        <div class="row g-4">


            <div class="col-md-4">

                <div class="glass-card p-4 h-100">

                    <i
                        class="
                            bi
                            bi-search
                            fs-1
                            text-gradient
                        "
                    ></i>

                    <h4 class="mt-3">
                        1. Pilih Kostum
                    </h4>

                    <p class="text-secondary">
                        Pilih kostum yang ingin kamu gunakan
                        dari katalog yang tersedia.
                    </p>

                </div>

            </div>



            <div class="col-md-4">

                <div class="glass-card p-4 h-100">

                    <i
                        class="
                            bi
                            bi-calendar-check
                            fs-1
                            text-gradient
                        "
                    ></i>

                    <h4 class="mt-3">
                        2. Tentukan Jadwal
                    </h4>

                    <p class="text-secondary">
                        Tentukan tanggal penggunaan dan
                        lakukan proses penyewaan.
                    </p>

                </div>

            </div>



            <div class="col-md-4">

                <div class="glass-card p-4 h-100">

                    <i
                        class="
                            bi
                            bi-box-seam
                            fs-1
                            text-gradient
                        "
                    ></i>

                    <h4 class="mt-3">
                        3. Gunakan Kostum
                    </h4>

                    <p class="text-secondary">
                        Kostum siap digunakan untuk event,
                        photoshoot, atau kegiatan cosplay.
                    </p>

                </div>

            </div>


        </div>

    </div>

</section>



<!-- =========================================================
     EVENT
========================================================= -->

<section
    id="event"
    class="py-5"
>

    <div class="container">

        <div class="glass-card p-5 text-center">

            <span
                class="
                    text-uppercase
                    text-gradient
                    fw-bold
                    small
                "
            >
                COSPLAYRENT
            </span>


            <h2 class="title-fantasy mt-2">

                SIAP WUJUDKAN

                <span class="text-gradient">
                    KARAKTERMU?
                </span>

            </h2>


            <p class="text-secondary">

                Pilih kostum favoritmu dan mulai
                pengalaman cosplay kamu sekarang.

            </p>


            <a
                href="#katalog"
                class="btn btn-neon mt-3"
            >

                Lihat Katalog

            </a>

        </div>

    </div>

</section>



<!-- =========================================================
     FOOTER
========================================================= -->

<footer
    style="
        background-color:#1e2130;
        text-align:center;
        padding:24px 16px;
        color:#cfd2dc;
    "
>

    <p
        style="
            margin:0 0 10px;
            font-size:15px;
        "
    >

        Sistem Penyewaan Kostum Cosplay

        &copy;

        <?= date('Y') ?>

        localhost/cosplayrent

    </p>


    <p
        style="
            margin:0;
            font-size:14px;
        "
    >

        <a
            href="#"
            style="
                color:#8f8cf5;
                text-decoration:none;
                margin-right:16px;
            "
        >
            @localhost/cosplayrent
        </a>


        <a
            href="#"
            style="
                color:#8f8cf5;
                text-decoration:none;
            "
        >
            Kebijakan Privasi
        </a>

    </p>

</footer>



<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
></script>



<!-- THEME SCRIPT -->

<script>

document.addEventListener('DOMContentLoaded', function () {


    const themeToggleBtn =
        document.getElementById('themeToggle');


    const htmlElement =
        document.documentElement;


    const savedTheme =
        localStorage.getItem('cosplayrent_theme')
        || 'dark';


    setTheme(savedTheme);



    themeToggleBtn.addEventListener(
        'click',
        function () {

            const currentTheme =
                htmlElement.getAttribute(
                    'data-bs-theme'
                );


            const newTheme =
                currentTheme === 'dark'
                    ? 'light'
                    : 'dark';


            setTheme(newTheme);

        }
    );



    function setTheme(theme) {

        htmlElement.setAttribute(
            'data-bs-theme',
            theme
        );


        localStorage.setItem(
            'cosplayrent_theme',
            theme
        );


        if (theme === 'light') {

            themeToggleBtn.innerHTML =
                '<i class="bi bi-moon-stars-fill" style="color:#ff9800;"></i>';


            themeToggleBtn.style.backgroundColor =
                '#ffffff';


            themeToggleBtn.style.borderColor =
                'rgba(176,0,255,.2)';

        } else {

            themeToggleBtn.innerHTML =
                '<i class="bi bi-sun-fill" style="color:#ffc107;"></i>';


            themeToggleBtn.style.backgroundColor =
                'rgba(255,255,255,.05)';


            themeToggleBtn.style.borderColor =
                'rgba(255,255,255,.1)';

        }

    }

});

</script>


</body>

</html>