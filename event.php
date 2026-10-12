<?php
// event.php

// Pastikan session aktif jika nantinya event membutuhkan login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Judul halaman
$page_title = "Jadwal Event - CosplayRent";
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $page_title; ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background-color: #0b0719;
            color: #fff;
            min-height: 100vh;
        }

        .event-card {
            background: #130d2a;
            border: 1px solid #2a1b54;
            border-left: 4px solid #b000ff;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: 0.3s;
        }

        .event-card:hover {
            transform: translateY(-3px);
            border-color: #b000ff;
            box-shadow: 0 10px 25px rgba(176, 0, 255, 0.15);
        }

        .event-title {
            color: #00d4ff;
            font-weight: 700;
        }

        .btn-event {
            color: #00d4ff;
            border-color: #00d4ff;
        }

        .btn-event:hover {
            background-color: #00d4ff;
            color: #000;
        }

        .page-title {
            color: #00d4ff;
            font-weight: 700;
        }

        .subtitle {
            color: #8d85a5;
        }

        .navbar-custom {
            background-color: #0b0719;
            border-bottom: 1px solid #24163f;
        }

        .logo {
            font-weight: 800;
            font-size: 22px;
            text-decoration: none;
        }

        .logo-cosplay {
            color: #00d4ff;
        }

        .logo-rent {
            color: #b000ff;
        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">

    <div class="container">

        <a
            href="index.php"
            class="logo"
        >

            <span class="logo-cosplay">
                COSPLAY
            </span>

            <span class="logo-rent">
                RENT
            </span>

        </a>


        <div class="d-flex gap-2">

            <a
                href="index.php"
                class="btn btn-outline-light btn-sm"
            >
                Beranda
            </a>

            <a
                href="katalog.php"
                class="btn btn-sm"
                style="
                    background:#b000ff;
                    color:white;
                "
            >
                Katalog
            </a>

        </div>

    </div>

</nav>



<!-- CONTENT -->

<div class="container my-5">

    <div class="text-center mb-4">

        <h2 class="page-title">
            JADWAL EVENT COSPLAY TERDEKAT
        </h2>

        <p class="subtitle">
            Temukan event cosplay dan persiapkan kostum favoritmu.
        </p>

    </div>


    <div class="row justify-content-center">

        <div class="col-md-8">


            <!-- EVENT 1 -->

            <div class="event-card p-3 d-flex align-items-center justify-content-between">

                <div>

                    <span class="badge bg-danger mb-1">
                        Mendatang
                    </span>

                    <h5 class="mb-1 text-white">
                        Comic Frontier (Comifuro) 18
                    </h5>

                    <p class="text-muted mb-0 small">

                        📍 ICE BSD, Tangerang

                        |

                        📅 25 - 26 Mei 2026

                    </p>

                </div>


                <a
                    href="booking.php"
                    class="btn btn-event btn-sm"
                >
                    Booking Kostum
                </a>

            </div>



            <!-- EVENT 2 -->

            <div class="event-card p-3 d-flex align-items-center justify-content-between">

                <div>

                    <span class="badge bg-danger mb-1">
                        Mendatang
                    </span>

                    <h5 class="mb-1 text-white">
                        Indonesia Anime Con (INACON)
                    </h5>

                    <p class="text-muted mb-0 small">

                        📍 JCC Senayan, Jakarta

                        |

                        📅 15 - 16 Juni 2026

                    </p>

                </div>


                <a
                    href="katalog.php"
                    class="btn btn-event btn-sm"
                >
                    Booking Kostum
                </a>

            </div>


        </div>

    </div>

</div>


</body>

</html>