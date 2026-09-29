<?php

require_once "koneksi.php";

/*
|--------------------------------------------------------------------------
| Proteksi halaman admin
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["peran"]) || $_SESSION["peran"] !== "admin") {
    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Data Admin
|--------------------------------------------------------------------------
*/

$namaAdmin = $_SESSION["nama"] ?? "Admin";


/*
|--------------------------------------------------------------------------
| Statistik Dashboard
|--------------------------------------------------------------------------
*/

$totalKostum = 0;
$totalPengguna = 0;
$totalPenyewaan = 0;
$totalKategori = 0;


/* Total Kostum */

$query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM kostum"
);

if ($query) {
    $data = mysqli_fetch_assoc($query);
    $totalKostum = $data["total"] ?? 0;
}


/* Total Pengguna */

$query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM pengguna"
);

if ($query) {
    $data = mysqli_fetch_assoc($query);
    $totalPengguna = $data["total"] ?? 0;
}


/* Total Penyewaan */

$query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM penyewaan"
);

if ($query) {
    $data = mysqli_fetch_assoc($query);
    $totalPenyewaan = $data["total"] ?? 0;
}


/* Total Kategori */

$query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM kategori"
);

if ($query) {
    $data = mysqli_fetch_assoc($query);
    $totalKategori = $data["total"] ?? 0;
}


/*
|--------------------------------------------------------------------------
| Penyewaan Terbaru
|--------------------------------------------------------------------------
*/

$penyewaanTerbaru = mysqli_query(
    $conn,
    "SELECT *
     FROM penyewaan
     ORDER BY id DESC
     LIMIT 5"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard Admin - CosplayRent</title>


    <!-- Google Font -->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            background: #0F172A;
            color: #f8fafc;
        }


        /* =========================================================
           SIDEBAR
        ========================================================= */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;

            background:
                linear-gradient(
                    180deg,
                    #111827 0%,
                    #0b1120 100%
                );

            border-right: 1px solid rgba(255,255,255,.08);

            padding: 25px 18px;

            z-index: 1000;
        }


        .brand {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 5px 10px 30px;
        }


        .brand-icon {
            width: 42px;
            height: 42px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 12px;

            background:
                linear-gradient(
                    135deg,
                    #7C3AED,
                    #2563EB
                );

            box-shadow:
                0 0 25px rgba(124,58,237,.35);

            font-size: 21px;
        }


        .brand-name {
            font-family: "Poppins", sans-serif;
            font-size: 20px;
            font-weight: 800;
        }


        .brand-name span {
            color: #8b5cf6;
        }


        .menu-title {
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;

            letter-spacing: 1.2px;

            padding: 0 12px;
            margin: 10px 0;
        }


        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }


        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 13px;

            padding: 12px 14px;

            color: #94a3b8;
            text-decoration: none;

            border-radius: 10px;

            transition: .25s ease;

            font-size: 14px;
            font-weight: 600;
        }


        .sidebar-menu a i {
            font-size: 18px;
            width: 22px;
        }


        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            color: white;

            background:
                linear-gradient(
                    90deg,
                    rgba(124,58,237,.22),
                    rgba(37,99,235,.10)
                );

            box-shadow:
                inset 3px 0 0 #8b5cf6;
        }


        .sidebar-bottom {
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 20px;
        }


        .back-home {
            display: flex;
            align-items: center;
            gap: 10px;

            color: #94a3b8;
            text-decoration: none;

            padding: 12px;

            border-radius: 10px;

            transition: .25s;
        }


        .back-home:hover {
            color: white;
            background: rgba(255,255,255,.05);
        }


        /* =========================================================
           MAIN
        ========================================================= */

        .main {
            margin-left: 260px;
            min-height: 100vh;
        }


        /* =========================================================
           TOPBAR
        ========================================================= */

        .topbar {
            height: 75px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 35px;

            background: rgba(15,23,42,.88);

            border-bottom: 1px solid rgba(255,255,255,.07);

            backdrop-filter: blur(12px);

            position: sticky;
            top: 0;
            z-index: 900;
        }


        .page-title {
            font-family: "Poppins", sans-serif;
            font-size: 20px;
            font-weight: 700;
        }


        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }


        .admin-avatar {
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background:
                linear-gradient(
                    135deg,
                    #7C3AED,
                    #2563EB
                );

            font-weight: 700;
        }


        .admin-info strong {
            display: block;
            font-size: 13px;
        }


        .admin-info small {
            color: #64748b;
            font-size: 11px;
        }


        /* =========================================================
           CONTENT
        ========================================================= */

        .content {
            padding: 35px;
        }


        .welcome {
            margin-bottom: 30px;
        }


        .welcome h1 {
            font-family: "Poppins", sans-serif;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 7px;
        }


        .welcome p {
            color: #94a3b8;
            margin: 0;
        }


        /* =========================================================
           STAT CARDS
        ========================================================= */

        .stats-grid {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 20px;

            margin-bottom: 30px;
        }


        .stat-card {
            position: relative;

            padding: 23px;

            border-radius: 18px;

            background:
                linear-gradient(
                    145deg,
                    rgba(30,41,59,.92),
                    rgba(15,23,42,.92)
                );

            border: 1px solid rgba(255,255,255,.07);

            overflow: hidden;

            transition: .25s;
        }


        .stat-card:hover {
            transform: translateY(-4px);

            border-color:
                rgba(139,92,246,.4);

            box-shadow:
                0 15px 40px rgba(0,0,0,.2);
        }


        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .stat-icon {
            width: 48px;
            height: 48px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 13px;

            background:
                rgba(124,58,237,.15);

            color: #a78bfa;

            font-size: 22px;
        }


        .stat-card h2 {
            font-size: 30px;
            font-weight: 800;

            margin: 18px 0 3px;
        }


        .stat-card p {
            color: #64748b;
            font-size: 13px;
            margin: 0;
        }


        /* =========================================================
           CONTENT GRID
        ========================================================= */

        .dashboard-grid {
            display: grid;

            grid-template-columns:
                1.5fr 1fr;

            gap: 20px;
        }


        .panel {
            background:
                linear-gradient(
                    145deg,
                    rgba(30,41,59,.92),
                    rgba(15,23,42,.92)
                );

            border:
                1px solid rgba(255,255,255,.07);

            border-radius: 18px;

            overflow: hidden;
        }


        .panel-header {
            padding: 20px 22px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            border-bottom:
                1px solid rgba(255,255,255,.06);
        }


        .panel-header h3 {
            font-family: "Poppins", sans-serif;
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }


        .panel-header a {
            color: #a78bfa;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }


        /* =========================================================
           TABLE
        ========================================================= */

        .table-wrapper {
            overflow-x: auto;
        }


        .table {
            margin: 0;
            color: #e2e8f0;
        }


        .table > :not(caption) > * > * {
            background: transparent;
            border-bottom-color:
                rgba(255,255,255,.06);
            padding: 15px 20px;
        }


        .table thead th {
            color: #64748b;
            font-size: 11px;

            text-transform: uppercase;
            letter-spacing: .7px;
        }


        .table tbody td {
            font-size: 13px;
        }


        .status {
            display: inline-block;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 11px;
            font-weight: 700;
        }


        .status-pending {
            color: #fbbf24;
            background: rgba(251,191,36,.12);
        }


        .status-success {
            color: #34d399;
            background: rgba(52,211,153,.12);
        }


        .status-danger {
            color: #fb7185;
            background: rgba(251,113,133,.12);
        }


        /* =========================================================
           QUICK MENU
        ========================================================= */

        .quick-menu {
            padding: 20px;
        }


        .quick-item {
            display: flex;
            align-items: center;
            gap: 15px;

            padding: 15px;

            margin-bottom: 10px;

            border-radius: 12px;

            background: rgba(255,255,255,.025);

            color: white;
            text-decoration: none;

            border: 1px solid transparent;

            transition: .25s;
        }


        .quick-item:last-child {
            margin-bottom: 0;
        }


        .quick-item:hover {
            border-color:
                rgba(139,92,246,.35);

            background:
                rgba(124,58,237,.08);

            transform: translateX(3px);
        }


        .quick-icon {
            width: 42px;
            height: 42px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 11px;

            background:
                rgba(124,58,237,.13);

            color: #a78bfa;

            font-size: 19px;
        }


        .quick-text strong {
            display: block;
            font-size: 13px;
        }


        .quick-text small {
            color: #64748b;
            font-size: 11px;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1100px) {

            .stats-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }


        @media (max-width: 768px) {

            .sidebar {
                width: 75px;
                padding: 20px 10px;
            }

            .brand-name,
            .menu-title,
            .sidebar-menu span,
            .back-home span {
                display: none;
            }

            .brand {
                justify-content: center;
                padding-bottom: 25px;
            }

            .sidebar-menu a {
                justify-content: center;
                padding: 13px;
            }

            .sidebar-bottom {
                left: 10px;
                right: 10px;
            }

            .main {
                margin-left: 75px;
            }

            .topbar {
                padding: 0 20px;
            }

            .content {
                padding: 25px 18px;
            }

            .admin-info {
                display: none;
            }
        }


        @media (max-width: 500px) {

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .welcome h1 {
                font-size: 23px;
            }

            .page-title {
                font-size: 17px;
            }
        }

    </style>

</head>


<body>


<!-- =========================================================
     SIDEBAR
========================================================= -->

<aside class="sidebar">


    <div class="brand">

        <div class="brand-icon">
            <i class="bi bi-stars"></i>
        </div>

        <div class="brand-name">
            Cosplay<span>Rent</span>
        </div>

    </div>


    <div class="menu-title">
        Menu Utama
    </div>


    <nav class="sidebar-menu">

        <a href="dashboard.php" class="active">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>


        <a href="kostum.php">
            <i class="bi bi-person-standing-dress"></i>
            <span>Data Kostum</span>
        </a>


        <a href="kategori.php">
            <i class="bi bi-tags-fill"></i>
            <span>Kategori</span>
        </a>


        <a href="pengguna.php">
            <i class="bi bi-people-fill"></i>
            <span>Pengguna</span>
        </a>


        <a href="penyewaan.php">
            <i class="bi bi-receipt"></i>
            <span>Penyewaan</span>
        </a>


        <a href="ulasan.php">
            <i class="bi bi-star-fill"></i>
            <span>Ulasan</span>
        </a>

    </nav>


    <div class="menu-title mt-4">
        Lainnya
    </div>


    <nav class="sidebar-menu">

        <a href="index.php">
            <i class="bi bi-house-fill"></i>
            <span>Halaman Utama</span>
        </a>


        <a href="logout.php"
           onclick="return confirm('Yakin ingin logout?')">

            <i class="bi bi-box-arrow-right"></i>

            <span>Logout</span>

        </a>

    </nav>


</aside>



<!-- =========================================================
     MAIN
========================================================= -->

<main class="main">


    <!-- TOPBAR -->

    <header class="topbar">

        <div class="page-title">
            Dashboard
        </div>


        <div class="admin-profile">

            <div class="admin-avatar">
                <?= strtoupper(substr($namaAdmin, 0, 1)) ?>
            </div>


            <div class="admin-info">

                <strong>
                    <?= htmlspecialchars($namaAdmin) ?>
                </strong>

                <small>
                    Administrator
                </small>

            </div>

        </div>

    </header>



    <!-- CONTENT -->

    <section class="content">


        <!-- WELCOME -->

        <div class="welcome">

            <h1>
                Halo, <?= htmlspecialchars($namaAdmin) ?> 👋
            </h1>

            <p>
                Kelola dan pantau sistem CosplayRent dari sini.
            </p>

        </div>



        <!-- =====================================================
             STATISTICS
        ====================================================== -->

        <div class="stats-grid">


            <!-- KOSTUM -->

            <div class="stat-card">

                <div class="stat-top">

                    <div>
                        <h2>
                            <?= number_format($totalKostum) ?>
                        </h2>

                        <p>
                            Total Kostum
                        </p>
                    </div>

                    <div class="stat-icon">
                        <i class="bi bi-person-standing-dress"></i>
                    </div>

                </div>

            </div>


            <!-- PENGGUNA -->

            <div class="stat-card">

                <div class="stat-top">

                    <div>
                        <h2>
                            <?= number_format($totalPengguna) ?>
                        </h2>

                        <p>
                            Total Pengguna
                        </p>
                    </div>

                    <div class="stat-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>

                </div>

            </div>


            <!-- PENYEWAAN -->

            <div class="stat-card">

                <div class="stat-top">

                    <div>
                        <h2>
                            <?= number_format($totalPenyewaan) ?>
                        </h2>

                        <p>
                            Total Penyewaan
                        </p>
                    </div>

                    <div class="stat-icon">
                        <i class="bi bi-receipt"></i>
                    </div>

                </div>

            </div>


            <!-- KATEGORI -->

            <div class="stat-card">

                <div class="stat-top">

                    <div>
                        <h2>
                            <?= number_format($totalKategori) ?>
                        </h2>

                        <p>
                            Total Kategori
                        </p>
                    </div>

                    <div class="stat-icon">
                        <i class="bi bi-tags-fill"></i>
                    </div>

                </div>

            </div>


        </div>



        <!-- =====================================================
             DASHBOARD CONTENT
        ====================================================== -->

        <div class="dashboard-grid">


            <!-- PENYEWAAN TERBARU -->

            <div class="panel">

                <div class="panel-header">

                    <h3>
                        Penyewaan Terbaru
                    </h3>

                    <a href="penyewaan.php">
                        Lihat semua
                    </a>

                </div>


                <div class="table-wrapper">

                    <table class="table">

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>Nama</th>
                                <th>Status</th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if ($penyewaanTerbaru && mysqli_num_rows($penyewaanTerbaru) > 0): ?>

                            <?php while ($row = mysqli_fetch_assoc($penyewaanTerbaru)): ?>

                                <?php

                                $status =
                                    strtolower(
                                        $row["status"]
                                        ?? "pending"
                                    );

                                $statusClass = "status-pending";

                                if (
                                    in_array(
                                        $status,
                                        [
                                            "selesai",
                                            "disetujui",
                                            "dibayar",
                                            "berhasil"
                                        ]
                                    )
                                ) {
                                    $statusClass =
                                        "status-success";
                                }

                                if (
                                    in_array(
                                        $status,
                                        [
                                            "dibatalkan",
                                            "ditolak"
                                        ]
                                    )
                                ) {
                                    $statusClass =
                                        "status-danger";
                                }

                                ?>

                                <tr>

                                    <td>
                                        #<?= htmlspecialchars($row["id"]) ?>
                                    </td>

                                    <td>
                                        Penyewaan
                                    </td>

                                    <td>

                                        <span
                                            class="status <?= $statusClass ?>"
                                        >
                                            <?= htmlspecialchars(
                                                ucfirst($status)
                                            ) ?>
                                        </span>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="3"
                                    class="text-center"
                                    style="color:#64748b;padding:35px;"
                                >
                                    Belum ada data penyewaan.
                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>



            <!-- MENU CEPAT -->

            <div class="panel">

                <div class="panel-header">

                    <h3>
                        Menu Cepat
                    </h3>

                </div>


                <div class="quick-menu">


                    <a href="kostum.php"
                       class="quick-item">

                        <div class="quick-icon">
                            <i class="bi bi-plus-lg"></i>
                        </div>

                        <div class="quick-text">

                            <strong>
                                Tambah Kostum
                            </strong>

                            <small>
                                Tambahkan kostum baru
                            </small>

                        </div>

                    </a>


                    <a href="kategori.php"
                       class="quick-item">

                        <div class="quick-icon">
                            <i class="bi bi-tag"></i>
                        </div>

                        <div class="quick-text">

                            <strong>
                                Kelola Kategori
                            </strong>

                            <small>
                                Atur kategori kostum
                            </small>

                        </div>

                    </a>


                    <a href="penyewaan.php"
                       class="quick-item">

                        <div class="quick-icon">
                            <i class="bi bi-receipt"></i>
                        </div>

                        <div class="quick-text">

                            <strong>
                                Kelola Penyewaan
                            </strong>

                            <small>
                                Periksa transaksi rental
                            </small>

                        </div>

                    </a>


                    <a href="pengguna.php"
                       class="quick-item">

                        <div class="quick-icon">
                            <i class="bi bi-person-plus"></i>
                        </div>

                        <div class="quick-text">

                            <strong>
                                Data Pengguna
                            </strong>

                            <small>
                                Lihat pengguna CosplayRent
                            </small>

                        </div>

                    </a>


                </div>

            </div>


        </div>


    </section>

</main>


</body>
</html>