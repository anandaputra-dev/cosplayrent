<?php

require_once "koneksi.php";

/*
|--------------------------------------------------------------------------
| Jika user sudah login
|--------------------------------------------------------------------------
*/

if (isset($_SESSION["user_id"])) {

    if (
        isset($_SESSION["peran"]) &&
        $_SESSION["peran"] === "admin"
    ) {
        header("Location: admin/dashboard.php");
        exit;
    }

    header("Location: dashboard.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Variabel
|--------------------------------------------------------------------------
*/

$error = "";
$success = "";

$nama = "";
$email = "";
$is_owner = "";


/*
|--------------------------------------------------------------------------
| Proses Register
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");

    $password = $_POST["password"] ?? "";
    $password_confirm = $_POST["password_confirm"] ?? "";

    $is_owner = $_POST["is_owner"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | Validasi Nama
    |--------------------------------------------------------------------------
    */

    if ($nama === "") {

        $error = "Nama lengkap wajib diisi.";

    }


    /*
    |--------------------------------------------------------------------------
    | Validasi Email
    |--------------------------------------------------------------------------
    */

    elseif ($email === "") {

        $error = "Email wajib diisi.";

    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Format email tidak valid.";

    }


    /*
    |--------------------------------------------------------------------------
    | Validasi Password
    |--------------------------------------------------------------------------
    */

    elseif ($password === "") {

        $error = "Password wajib diisi.";

    }

    elseif (strlen($password) < 6) {

        $error = "Password minimal 6 karakter.";

    }


    /*
    |--------------------------------------------------------------------------
    | Konfirmasi Password
    |--------------------------------------------------------------------------
    */

    elseif ($password !== $password_confirm) {

        $error = "Konfirmasi password tidak sama.";

    }


    /*
    |--------------------------------------------------------------------------
    | Validasi Role
    |--------------------------------------------------------------------------
    */

    elseif ($is_owner !== "Iya" && $is_owner !== "Tidak") {

        $error = "Silakan pilih jenis akun.";

    }


    /*
    |--------------------------------------------------------------------------
    | Jika validasi berhasil
    |--------------------------------------------------------------------------
    */

    else {

        /*
        |--------------------------------------------------------------------------
        | Cek Email
        |--------------------------------------------------------------------------
        */

        $sqlCheck = "
            SELECT id
            FROM pengguna
            WHERE email = ?
            LIMIT 1
        ";

        $stmtCheck = $conn->prepare($sqlCheck);


        if (!$stmtCheck) {

            $error = "Terjadi kesalahan pada sistem.";

        } else {

            $stmtCheck->bind_param("s", $email);

            $stmtCheck->execute();

            $resultCheck = $stmtCheck->get_result();


            /*
            |--------------------------------------------------------------------------
            | Email sudah terdaftar
            |--------------------------------------------------------------------------
            */

            if ($resultCheck->num_rows > 0) {

                $error = "Email tersebut sudah terdaftar.";

            }


            /*
            |--------------------------------------------------------------------------
            | Email belum terdaftar
            |--------------------------------------------------------------------------
            */

            else {

                /*
                |--------------------------------------------------------------------------
                | Hash Password
                |--------------------------------------------------------------------------
                */

                $hashedPassword =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


                /*
                |--------------------------------------------------------------------------
                | Tentukan Role
                |--------------------------------------------------------------------------
                */

                if ($is_owner === "Iya") {

                    $peran = "admin";

                } else {

                    $peran = "pelanggan";
                }


                /*
                |--------------------------------------------------------------------------
                | Insert User
                |--------------------------------------------------------------------------
                */

                $sqlInsert = "
                    INSERT INTO pengguna
                    (
                        nama_lengkap,
                        email,
                        kata_sandi,
                        peran
                    )
                    VALUES (?, ?, ?, ?)
                ";

                $stmtInsert = $conn->prepare($sqlInsert);


                if (!$stmtInsert) {

                    $error =
                        "Terjadi kesalahan saat membuat akun.";

                } else {

                    $stmtInsert->bind_param(
                        "ssss",
                        $nama,
                        $email,
                        $hashedPassword,
                        $peran
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Jalankan Insert
                    |--------------------------------------------------------------------------
                    */

                    if ($stmtInsert->execute()) {

                        $success =
                            "Registrasi berhasil. Silakan login.";

                        $nama = "";
                        $email = "";
                        $is_owner = "";

                    } else {

                        $error =
                            "Registrasi gagal. Silakan coba lagi.";
                    }


                    $stmtInsert->close();
                }
            }


            $stmtCheck->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Daftar - CosplayRent</title>


    <!-- Google Font -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


<style>

/* =========================================================
   RESET
========================================================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


/* =========================================================
   BODY
========================================================= */

body {

    min-height: 100vh;

    font-family: "Plus Jakarta Sans", sans-serif;

    color: #fff;

    background:
        radial-gradient(
            circle at 10% 20%,
            rgba(124, 58, 237, .30),
            transparent 30%
        ),
        radial-gradient(
            circle at 90% 80%,
            rgba(0, 212, 255, .20),
            transparent 30%
        ),
        #070b18;

    overflow-x: hidden;

}


/* =========================================================
   BACKGROUND EFFECT
========================================================= */

body::before {

    content: "";

    position: fixed;

    width: 450px;
    height: 450px;

    border-radius: 50%;

    background: rgba(124, 58, 237, .15);

    filter: blur(100px);

    top: -150px;
    left: -100px;

    pointer-events: none;

}


body::after {

    content: "";

    position: fixed;

    width: 400px;
    height: 400px;

    border-radius: 50%;

    background: rgba(0, 212, 255, .12);

    filter: blur(100px);

    bottom: -150px;
    right: -100px;

    pointer-events: none;

}


/* =========================================================
   PAGE
========================================================= */

.auth-page {

    min-height: 100vh;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 40px 20px;

}


/* =========================================================
   CARD
========================================================= */

.auth-card {

    width: 100%;

    max-width: 1100px;

    min-height: 650px;

    display: grid;

    grid-template-columns: .9fr 1.1fr;

    overflow: hidden;

    border-radius: 30px;

    border: 1px solid rgba(255,255,255,.12);

    background: rgba(15,23,42,.72);

    backdrop-filter: blur(25px);

    -webkit-backdrop-filter: blur(25px);

    box-shadow:
        0 30px 100px rgba(0,0,0,.55),
        0 0 80px rgba(124,58,237,.08);

    animation: cardAppear .8s ease;

}


/* =========================================================
   LEFT SIDE
========================================================= */

.auth-card::before {

    content: "";

    position: absolute;

}


.auth-info {

    position: relative;

    padding: 55px;

    display: flex;

    flex-direction: column;

    justify-content: center;

    background:
        linear-gradient(
            145deg,
            rgba(124,58,237,.28),
            rgba(37,99,235,.12)
        );

    border-right: 1px solid rgba(255,255,255,.08);

    overflow: hidden;

}


/* Decorative circle */

.auth-info::before {

    content: "";

    position: absolute;

    width: 300px;
    height: 300px;

    border: 1px solid rgba(255,255,255,.08);

    border-radius: 50%;

    top: -100px;
    right: -120px;

}


.auth-info::after {

    content: "";

    position: absolute;

    width: 200px;
    height: 200px;

    border: 1px solid rgba(0,212,255,.12);

    border-radius: 50%;

    bottom: -80px;
    left: -80px;

}


/* =========================================================
   LOGO
========================================================= */

.brand {

    position: relative;

    z-index: 2;

    font-family: "Cinzel", serif;

    font-size: 30px;

    font-weight: 900;

    margin-bottom: 45px;

    letter-spacing: .5px;

}


.brand span {

    color: #8b5cf6;

    text-shadow:
        0 0 20px rgba(139,92,246,.5);

}


/* =========================================================
   LEFT TITLE
========================================================= */

.auth-info h1 {

    position: relative;

    z-index: 2;

    font-family: "Cinzel", serif;

    font-size: 43px;

    line-height: 1.15;

    margin-bottom: 20px;

}


.gradient-text {

    background:
        linear-gradient(
            90deg,
            #a78bfa,
            #38bdf8
        );

    -webkit-background-clip: text;

    -webkit-text-fill-color: transparent;

}


.auth-info p {

    position: relative;

    z-index: 2;

    color: #aab5c8;

    line-height: 1.8;

    max-width: 390px;

}


/* =========================================================
   FEATURES
========================================================= */

.features {

    position: relative;

    z-index: 2;

    margin-top: 35px;

    display: flex;

    flex-direction: column;

    gap: 16px;

}


.feature {

    display: flex;

    align-items: center;

    gap: 13px;

    color: #dbe4f0;

    font-size: 14px;

}


.feature-icon {

    width: 38px;
    height: 38px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 11px;

    background: rgba(124,58,237,.18);

    border: 1px solid rgba(139,92,246,.25);

    color: #a78bfa;

}


/* =========================================================
   RIGHT SIDE
========================================================= */

.auth-form-area {

    padding: 50px 60px;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow-y: auto;

}


.form-container {

    width: 100%;

    max-width: 500px;

}


/* =========================================================
   TITLE
========================================================= */

.auth-title {

    font-size: 30px;

    font-weight: 800;

    margin-bottom: 8px;

}


.auth-subtitle {

    color: #8995a9;

    font-size: 14px;

    margin-bottom: 28px;

}


/* =========================================================
   ALERT
========================================================= */

.auth-alert {

    display: flex;

    align-items: center;

    gap: 8px;

    padding: 13px 15px;

    margin-bottom: 20px;

    border-radius: 12px;

    font-size: 13px;

    animation: alertAppear .4s ease;

}


.auth-alert.error {

    background: rgba(239,68,68,.10);

    border: 1px solid rgba(239,68,68,.25);

    color: #fca5a5;

}


.auth-alert.success {

    background: rgba(34,197,94,.10);

    border: 1px solid rgba(34,197,94,.25);

    color: #86efac;

}


/* =========================================================
   FORM GROUP
========================================================= */

.auth-group {

    margin-bottom: 18px;

}


.auth-group label {

    display: block;

    margin-bottom: 8px;

    color: #d9e0eb;

    font-size: 13px;

    font-weight: 700;

}


/* =========================================================
   INPUT
========================================================= */

.input-box {

    position: relative;

}


.input-icon {

    position: absolute;

    left: 15px;

    top: 50%;

    transform: translateY(-50%);

    color: #718096;

    pointer-events: none;

    transition: .3s;

}


.input-box input {

    width: 100%;

    height: 50px;

    padding: 0 45px;

    border-radius: 13px;

    border: 1px solid rgba(255,255,255,.10);

    outline: none;

    background: rgba(255,255,255,.045);

    color: white;

    font-family: inherit;

    font-size: 13px;

    transition: .3s;

}


.input-box input::placeholder {

    color: #596579;

}


.input-box input:hover {

    border-color: rgba(139,92,246,.3);

}


.input-box input:focus {

    border-color: #7c3aed;

    background: rgba(124,58,237,.06);

    box-shadow:
        0 0 0 3px rgba(124,58,237,.10),
        0 0 25px rgba(124,58,237,.08);

}


.input-box input:focus + .input-icon {

    color: #a78bfa;

}


/* =========================================================
   PASSWORD
========================================================= */

.password-box {

    position: relative;

}


.password-box input {

    padding-right: 50px;

}


.password-toggle {

    position: absolute;

    right: 14px;

    top: 50%;

    transform: translateY(-50%);

    width: 32px;
    height: 32px;

    display: flex;

    align-items: center;
    justify-content: center;

    border: none;

    background: transparent;

    color: #718096;

    cursor: pointer;

    border-radius: 8px;

    transition: .2s;

}


.password-toggle:hover {

    color: #a78bfa;

    background: rgba(124,58,237,.1);

}


/* =========================================================
   PASSWORD STRENGTH
========================================================= */

.password-strength {

    margin-top: 9px;

}


.strength-bars {

    display: flex;

    gap: 5px;

    margin-bottom: 5px;

}


.strength-bar {

    flex: 1;

    height: 3px;

    border-radius: 10px;

    background: #273244;

    transition: .3s;

}


.strength-text {

    font-size: 11px;

    color: #64748b;

}


/* =========================================================
   ROLE
========================================================= */

.auth-role {

    margin-top: 5px;

    margin-bottom: 20px;

}


.role-title {

    color: #d9e0eb;

    font-size: 13px;

    font-weight: 700;

    margin-bottom: 10px;

}


.role-options {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 10px;

}


.role-option {

    position: relative;

}


.role-option input {

    position: absolute;

    opacity: 0;

}


.role-card {

    min-height: 75px;

    padding: 12px;

    display: flex;

    align-items: center;

    gap: 11px;

    cursor: pointer;

    border-radius: 13px;

    border: 1px solid rgba(255,255,255,.09);

    background: rgba(255,255,255,.035);

    transition: .25s;

}


.role-card:hover {

    transform: translateY(-2px);

    border-color: rgba(139,92,246,.4);

    background: rgba(124,58,237,.07);

}


.role-option input:checked + .role-card {

    border-color: #7c3aed;

    background:
        linear-gradient(
            135deg,
            rgba(124,58,237,.18),
            rgba(37,99,235,.08)
        );

    box-shadow:
        0 0 25px rgba(124,58,237,.08);

}


.role-icon {

    width: 38px;
    height: 38px;

    flex-shrink: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: rgba(124,58,237,.13);

    color: #a78bfa;

}


.role-text strong {

    display: block;

    font-size: 12px;

    color: #e8edf5;

    margin-bottom: 3px;

}


.role-text small {

    display: block;

    color: #6f7b8f;

    font-size: 10px;

}


/* =========================================================
   BUTTON
========================================================= */

.auth-button {

    width: 100%;

    height: 52px;

    border: none;

    border-radius: 13px;

    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #2563eb
        );

    color: white;

    font-family: inherit;

    font-size: 14px;

    font-weight: 800;

    cursor: pointer;

    box-shadow:
        0 10px 30px rgba(124,58,237,.22);

    transition: .3s;

}


.auth-button:hover {

    transform: translateY(-2px);

    box-shadow:
        0 15px 40px rgba(124,58,237,.35);

}


.auth-button:active {

    transform: translateY(0);

}


/* =========================================================
   FOOTER
========================================================= */

.auth-footer {

    text-align: center;

    margin-top: 22px;

    color: #69758a;

    font-size: 12px;

}


.auth-footer a {

    color: #a78bfa;

    text-decoration: none;

    font-weight: 700;

}


.auth-footer a:hover {

    color: #c4b5fd;

}


/* =========================================================
   BACK
========================================================= */

.auth-back {

    display: flex;

    justify-content: center;

    align-items: center;

    margin-top: 18px;

    color: #64748b;

    text-decoration: none;

    font-size: 11px;

    transition: .2s;

}


.auth-back:hover {

    color: #a78bfa;

}


/* =========================================================
   ANIMATION
========================================================= */

@keyframes cardAppear {

    from {

        opacity: 0;

        transform: translateY(25px) scale(.98);

    }

    to {

        opacity: 1;

        transform: translateY(0) scale(1);

    }

}


@keyframes alertAppear {

    from {

        opacity: 0;

        transform: translateY(-8px);

    }

    to {

        opacity: 1;

        transform: translateY(0);

    }

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 900px) {

    .auth-card {

        grid-template-columns: 1fr;

        max-width: 600px;

    }

    .auth-info {

        display: none;

    }

    .auth-form-area {

        padding: 45px 35px;

    }

}


@media (max-width: 500px) {

    .auth-page {

        padding: 15px;

    }

    .auth-card {

        border-radius: 22px;

    }

    .auth-form-area {

        padding: 35px 22px;

    }

    .auth-title {

        font-size: 26px;

    }

    .role-options {

        grid-template-columns: 1fr;

    }

}

</style>

</head>


<body>


<div class="auth-page">

    <div class="auth-card">


        <!-- =====================================================
             LEFT INFORMATION
        ====================================================== -->

        <div class="auth-info">

            <div class="brand">
                Cosplay<span>Rent</span>
            </div>


            <h1>
                Mulai Petualangan
                <span class="gradient-text">
                    Cosplay-mu
                </span>
            </h1>


            <p>
                Buat akun CosplayRent dan temukan berbagai
                kostum untuk event, cosplay, photoshoot,
                dan momen spesialmu.
            </p>


            <div class="features">

                <div class="feature">

                    <div class="feature-icon">
                        <i class="bi bi-stars"></i>
                    </div>

                    <span>
                        Koleksi kostum yang beragam
                    </span>

                </div>


                <div class="feature">

                    <div class="feature-icon">
                        <i class="bi bi-search"></i>
                    </div>

                    <span>
                        Cari kostum dengan mudah
                    </span>

                </div>


                <div class="feature">

                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>

                    <span>
                        Akun dan data tersimpan dengan aman
                    </span>

                </div>


                <div class="feature">

                    <div class="feature-icon">
                        <i class="bi bi-lightning-charge"></i>
                    </div>

                    <span>
                        Proses penyewaan lebih praktis
                    </span>

                </div>

            </div>

        </div>


        <!-- =====================================================
             FORM
        ====================================================== -->

        <div class="auth-form-area">

            <div class="form-container">


                <h2 class="auth-title">
                    Buat Akun
                </h2>


                <p class="auth-subtitle">
                    Daftar sekarang dan mulai gunakan CosplayRent.
                </p>


                <!-- ERROR -->

                <?php if ($error !== ""): ?>

                    <div class="auth-alert error">

                        <i class="bi bi-exclamation-circle"></i>

                        <span>
                            <?= htmlspecialchars($error) ?>
                        </span>

                    </div>

                <?php endif; ?>


                <!-- SUCCESS -->

                <?php if ($success !== ""): ?>

                    <div class="auth-alert success">

                        <i class="bi bi-check-circle"></i>

                        <span>
                            <?= htmlspecialchars($success) ?>
                        </span>

                    </div>

                <?php endif; ?>


                <form
                    method="POST"
                    action=""
                    id="registerForm"
                    autocomplete="on"
                >


                    <!-- NAMA -->

                    <div class="auth-group">

                        <label for="nama">
                            Nama Lengkap
                        </label>

                        <div class="input-box">

                            <input
                                type="text"
                                id="nama"
                                name="nama"
                                value="<?= htmlspecialchars($nama) ?>"
                                placeholder="Masukkan nama lengkap"
                                autocomplete="name"
                                required
                            >

                            <i class="bi bi-person input-icon"></i>

                        </div>

                    </div>


                    <!-- EMAIL -->

                    <div class="auth-group">

                        <label for="email">
                            Email
                        </label>

                        <div class="input-box">

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?= htmlspecialchars($email) ?>"
                                placeholder="nama@email.com"
                                autocomplete="email"
                                required
                            >

                            <i class="bi bi-envelope input-icon"></i>

                        </div>

                    </div>


                    <!-- PASSWORD -->

                    <div class="auth-group">

                        <label for="password">
                            Password
                        </label>

                        <div class="input-box password-box">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Minimal 6 karakter"
                                autocomplete="new-password"
                                required
                            >

                            <i class="bi bi-lock input-icon"></i>

                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePassword"
                                aria-label="Tampilkan password"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                        </div>


                        <div class="password-strength">

                            <div class="strength-bars">

                                <div class="strength-bar"></div>
                                <div class="strength-bar"></div>
                                <div class="strength-bar"></div>
                                <div class="strength-bar"></div>

                            </div>

                            <div
                                class="strength-text"
                                id="strengthText"
                            >
                                Masukkan password

                            </div>

                        </div>

                    </div>


                    <!-- CONFIRM PASSWORD -->

                    <div class="auth-group">

                        <label for="password_confirm">
                            Konfirmasi Password
                        </label>

                        <div class="input-box password-box">

                            <input
                                type="password"
                                id="password_confirm"
                                name="password_confirm"
                                placeholder="Ulangi password"
                                autocomplete="new-password"
                                required
                            >

                            <i class="bi bi-shield-lock input-icon"></i>

                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePasswordConfirm"
                                aria-label="Tampilkan konfirmasi password"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                        </div>

                    </div>


                    <!-- ROLE -->

                    <div class="auth-role">

                        <div class="role-title">
                            Jenis Akun
                        </div>


                        <div class="role-options">


                            <!-- PELANGGAN -->

                            <label class="role-option">

                                <input
                                    type="radio"
                                    name="is_owner"
                                    value="Tidak"

                                    <?= $is_owner === "Tidak"
                                        ? "checked"
                                        : "" ?>

                                    required
                                >

                                <div class="role-card">

                                    <div class="role-icon">
                                        <i class="bi bi-person"></i>
                                    </div>

                                    <div class="role-text">

                                        <strong>
                                            Pelanggan
                                        </strong>

                                        <small>
                                            Menyewa kostum
                                        </small>

                                    </div>

                                </div>

                            </label>


                            <!-- ADMIN -->

                            <label class="role-option">

                                <input
                                    type="radio"
                                    name="is_owner"
                                    value="Iya"

                                    <?= $is_owner === "Iya"
                                        ? "checked"
                                        : "" ?>
                                >

                                <div class="role-card">

                                    <div class="role-icon">
                                        <i class="bi bi-shop"></i>
                                    </div>

                                    <div class="role-text">

                                        <strong>
                                            Owner / Admin
                                        </strong>

                                        <small>
                                            Mengelola kostum
                                        </small>

                                    </div>

                                </div>

                            </label>


                        </div>

                    </div>


                    <!-- BUTTON -->

                    <button
                        type="submit"
                        class="auth-button"
                        id="registerButton"
                    >

                        <i class="bi bi-person-plus"></i>

                        <span>
                            Buat Akun
                        </span>

                    </button>


                </form>


                <!-- LOGIN -->

                <div class="auth-footer">

                    Sudah punya akun?

                    <a href="login.php">
                        Login sekarang
                    </a>

                </div>


                <!-- BACK -->

                <a
                    href="index.php"
                    class="auth-back"
                >

                    <i class="bi bi-arrow-left"></i>

                    &nbsp; Kembali ke Beranda

                </a>


            </div>

        </div>

    </div>

</div>


<script>

/* =========================================================
   PASSWORD TOGGLE
========================================================= */

function setupPasswordToggle(buttonId, inputId) {

    const button =
        document.getElementById(buttonId);

    const input =
        document.getElementById(inputId);

    if (!button || !input) {
        return;
    }

    button.addEventListener("click", function () {

        const icon =
            this.querySelector("i");

        if (input.type === "password") {

            input.type = "text";

            icon.classList.remove("bi-eye");

            icon.classList.add("bi-eye-slash");

            this.setAttribute(
                "aria-label",
                "Sembunyikan password"
            );

        } else {

            input.type = "password";

            icon.classList.remove("bi-eye-slash");

            icon.classList.add("bi-eye");

            this.setAttribute(
                "aria-label",
                "Tampilkan password"
            );

        }

    });

}


setupPasswordToggle(
    "togglePassword",
    "password"
);


setupPasswordToggle(
    "togglePasswordConfirm",
    "password_confirm"
);


/* =========================================================
   PASSWORD STRENGTH
========================================================= */

const password =
    document.getElementById("password");

const strengthBars =
    document.querySelectorAll(".strength-bar");

const strengthText =
    document.getElementById("strengthText");


if (password) {

    password.addEventListener("input", function () {

        const value = this.value;

        let score = 0;

        if (value.length >= 6) {
            score++;
        }

        if (value.length >= 8) {
            score++;
        }

        if (/[A-Z]/.test(value)) {
            score++;
        }

        if (/[0-9]/.test(value)) {
            score++;
        }


        strengthBars.forEach(function (bar, index) {

            bar.style.background =
                index < score
                    ? "#8b5cf6"
                    : "#273244";

        });


        if (value.length === 0) {

            strengthText.textContent =
                "Masukkan password";

        } else if (score <= 1) {

            strengthText.textContent =
                "Password lemah";

        } else if (score === 2) {

            strengthText.textContent =
                "Password cukup";

        } else if (score === 3) {

            strengthText.textContent =
                "Password kuat";

        } else {

            strengthText.textContent =
                "Password sangat kuat";

        }

    });

}


/* =========================================================
   CONFIRM PASSWORD
========================================================= */

const confirmPassword =
    document.getElementById("password_confirm");


if (confirmPassword && password) {

    confirmPassword.addEventListener(
        "input",
        function () {

            if (this.value === "") {

                this.style.borderColor =
                    "rgba(255,255,255,.10)";

                return;
            }


            if (this.value === password.value) {

                this.style.borderColor =
                    "#22c55e";

            } else {

                this.style.borderColor =
                    "#ef4444";

            }

        }
    );

}


/* =========================================================
   SUBMIT BUTTON
========================================================= */

const registerForm =
    document.getElementById("registerForm");

const registerButton =
    document.getElementById("registerButton");


if (registerForm && registerButton) {

    registerForm.addEventListener(
        "submit",
        function () {

            registerButton.disabled = true;

            registerButton.innerHTML = `
                <i class="bi bi-arrow-repeat"></i>
                <span>Membuat akun...</span>
            `;

        }
    );

}

</script>


</body>

</html>