<?php

require_once "koneksi.php";

/*
|--------------------------------------------------------------------------
| Jika user sudah login
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['user_id'])) {

    if (
        isset($_SESSION['peran']) &&
        $_SESSION['peran'] === 'admin'
    ) {

        header("Location: dashboard_admin.php");
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
$email = "";


/*
|--------------------------------------------------------------------------
| Proses Login
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */

    if ($email === "" || $password === "") {

        $error = "Email dan password wajib diisi.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Format email tidak valid.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Cari user berdasarkan email
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT
                id,
                nama_lengkap,
                email,
                kata_sandi,
                peran
            FROM pengguna
            WHERE email = ?
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {

            $error = "Terjadi kesalahan pada sistem.";

        } else {

            $stmt->bind_param("s", $email);

            $stmt->execute();

            $result = $stmt->get_result();


            /*
            |--------------------------------------------------------------------------
            | User ditemukan
            |--------------------------------------------------------------------------
            */

            if ($result->num_rows === 1) {

                $user = $result->fetch_assoc();


                /*
                |--------------------------------------------------------------------------
                | Verifikasi password
                |--------------------------------------------------------------------------
                */

                if (
                    password_verify(
                        $password,
                        $user["kata_sandi"]
                    )
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Regenerasi session
                    |--------------------------------------------------------------------------
                    */

                    session_regenerate_id(true);


                    /*
                    |--------------------------------------------------------------------------
                    | Simpan session
                    |--------------------------------------------------------------------------
                    */

                    $_SESSION["user_id"] = $user["id"];

                    $_SESSION["nama"] =
                        $user["nama_lengkap"];

                    $_SESSION["email"] =
                        $user["email"];

                    $_SESSION["peran"] =
                        $user["peran"];


                    /*
                    |--------------------------------------------------------------------------
                    | Redirect berdasarkan role
                    |--------------------------------------------------------------------------
                    */

                    if ($user["peran"] === "admin") {

                        header(
                            "Location: dashboard_admin.php"
                        );

                        exit;

                    } else {

                        header(
                            "Location: dashboard.php"
                        );

                        exit;

                    }

                } else {

                    $error =
                        "Email atau password salah.";

                }

            } else {

                $error =
                    "Email atau password salah.";

            }

            $stmt->close();

        }

    }

}

?>

<!DOCTYPE html>

<html lang="id" data-bs-theme="dark">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Login ke akun CosplayRent"
    >

    <title>
        Login - CosplayRent
    </title>


    <!-- =====================================================
         GOOGLE FONT
    ====================================================== -->

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


    <!-- =====================================================
         BOOTSTRAP ICONS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <!-- =====================================================
         CSS UTAMA
    ====================================================== -->

    <link
        rel="stylesheet"
        href="style.css"
    >


    <!-- =====================================================
         CSS LOGIN
    ====================================================== -->

    <style>

        /* =====================================================
           RESET
        ====================================================== */

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            min-height: 100vh;

            font-family:
                'Plus Jakarta Sans',
                sans-serif;

            background:
                radial-gradient(
                    circle at 15% 20%,
                    rgba(124, 58, 237, .20),
                    transparent 35%
                ),

                radial-gradient(
                    circle at 85% 80%,
                    rgba(6, 182, 212, .15),
                    transparent 35%
                ),

                #070b18;

            color: white;

            overflow-x: hidden;

        }


        /* =====================================================
           BACKGROUND GLOW
        ====================================================== */

        .login-bg {

            position: fixed;

            inset: 0;

            pointer-events: none;

            overflow: hidden;

            z-index: 0;

        }


        .login-orb {

            position: absolute;

            border-radius: 50%;

            filter: blur(70px);

            opacity: .25;

            animation:
                floatOrb 8s ease-in-out infinite;

        }


        .orb-one {

            width: 280px;

            height: 280px;

            background: #7c3aed;

            top: -100px;

            left: -80px;

        }


        .orb-two {

            width: 240px;

            height: 240px;

            background: #06b6d4;

            bottom: -80px;

            right: -60px;

            animation-delay: -3s;

        }


        .orb-three {

            width: 150px;

            height: 150px;

            background: #a855f7;

            top: 45%;

            right: 20%;

            animation-delay: -5s;

        }


        @keyframes floatOrb {

            0%,
            100% {

                transform:
                    translate(0, 0)
                    scale(1);

            }

            50% {

                transform:
                    translate(20px, -25px)
                    scale(1.08);

            }

        }


        /* =====================================================
           PAGE
        ====================================================== */

        .login-page {

            position: relative;

            z-index: 1;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 40px 20px;

        }


        /* =====================================================
           MAIN CONTAINER
        ====================================================== */

        .login-container {

            width: 100%;

            max-width: 1050px;

            min-height: 650px;

            display: grid;

            grid-template-columns:
                1fr 1fr;

            border-radius: 28px;

            overflow: hidden;

            background:
                rgba(15, 23, 42, .76);

            border:
                1px solid
                rgba(255, 255, 255, .10);

            box-shadow:
                0 35px 100px
                rgba(0, 0, 0, .55);

            backdrop-filter:
                blur(25px);

            animation:
                loginAppear .7s ease both;

        }


        @keyframes loginAppear {

            from {

                opacity: 0;

                transform:
                    translateY(30px)
                    scale(.98);

            }

            to {

                opacity: 1;

                transform:
                    translateY(0)
                    scale(1);

            }

        }


        /* =====================================================
           LEFT PANEL
        ====================================================== */

        .login-visual {

            position: relative;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 50px;

            overflow: hidden;

            background:

                linear-gradient(
                    145deg,
                    rgba(124, 58, 237, .18),
                    rgba(6, 182, 212, .06)
                );

            border-right:
                1px solid
                rgba(255,255,255,.08);

        }


        .visual-content {

            position: relative;

            z-index: 2;

            max-width: 390px;

        }


        .visual-logo {

            display: inline-flex;

            align-items: center;

            gap: 12px;

            margin-bottom: 35px;

        }


        .visual-logo-icon {

            width: 52px;

            height: 52px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 16px;

            background:
                linear-gradient(
                    135deg,
                    #06b6d4,
                    #7c3aed
                );

            box-shadow:
                0 10px 30px
                rgba(124,58,237,.35);

            font-size: 25px;

        }


        .visual-logo-text {

            font-family: 'Cinzel', serif;

            font-size: 22px;

            font-weight: 800;

            letter-spacing: 2px;

        }


        .visual-logo-text span {

            color: #a855f7;

        }


        .visual-title {

            font-family: 'Cinzel', serif;

            font-size: clamp(
                34px,
                4vw,
                52px
            );

            line-height: 1.15;

            font-weight: 900;

            margin-bottom: 20px;

        }


        .visual-title span {

            background:
                linear-gradient(
                    135deg,
                    #22d3ee,
                    #a855f7
                );

            -webkit-background-clip: text;

            background-clip: text;

            color: transparent;

        }


        .visual-description {

            color: #94a3b8;

            line-height: 1.8;

            font-size: 15px;

            margin-bottom: 35px;

        }


        /* =====================================================
           FEATURE
        ====================================================== */

        .visual-features {

            display: grid;

            gap: 14px;

        }


        .visual-feature {

            display: flex;

            align-items: center;

            gap: 13px;

            color: #cbd5e1;

            font-size: 14px;

        }


        .visual-feature-icon {

            width: 35px;

            height: 35px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 10px;

            background:
                rgba(124,58,237,.15);

            color: #c084fc;

            border:
                1px solid
                rgba(168,85,247,.2);

        }


        /* =====================================================
           DECORATION
        ====================================================== */

        .visual-circle {

            position: absolute;

            border-radius: 50%;

            border:
                1px solid
                rgba(168,85,247,.15);

        }


        .circle-one {

            width: 400px;

            height: 400px;

            right: -210px;

            top: -160px;

        }


        .circle-two {

            width: 280px;

            height: 280px;

            left: -170px;

            bottom: -160px;

        }


        /* =====================================================
           LOGIN FORM PANEL
        ====================================================== */

        .login-form-panel {

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 50px;

        }


        .login-form-wrapper {

            width: 100%;

            max-width: 390px;

        }


        /* =====================================================
           MOBILE LOGO
        ====================================================== */

        .mobile-logo {

            display: none;

            text-align: center;

            font-family: 'Cinzel', serif;

            font-size: 27px;

            font-weight: 900;

            margin-bottom: 25px;

        }


        .mobile-logo span {

            color: #a855f7;

        }


        /* =====================================================
           TITLE
        ====================================================== */

        .login-title {

            font-family: 'Cinzel', serif;

            font-size: 34px;

            font-weight: 800;

            margin-bottom: 8px;

        }


        .login-title span {

            color: #a855f7;

        }


        .login-subtitle {

            color: #94a3b8;

            font-size: 14px;

            margin-bottom: 30px;

        }


        /* =====================================================
           ALERT
        ====================================================== */

        .login-alert {

            display: flex;

            align-items: flex-start;

            gap: 10px;

            padding: 13px 15px;

            margin-bottom: 22px;

            border-radius: 13px;

            background:
                rgba(239,68,68,.10);

            border:
                1px solid
                rgba(239,68,68,.25);

            color: #fca5a5;

            font-size: 13px;

            animation:
                shakeAlert .4s ease;

        }


        @keyframes shakeAlert {

            0%,
            100% {

                transform: translateX(0);

            }

            25% {

                transform: translateX(-5px);

            }

            75% {

                transform: translateX(5px);

            }

        }


        /* =====================================================
           FORM
        ====================================================== */

        .login-group {

            margin-bottom: 20px;

        }


        .login-group label {

            display: block;

            margin-bottom: 8px;

            color: #e2e8f0;

            font-size: 13px;

            font-weight: 600;

        }


        .login-input-wrapper {

            position: relative;

        }


        .login-input-icon {

            position: absolute;

            left: 16px;

            top: 50%;

            transform:
                translateY(-50%);

            color: #64748b;

            font-size: 17px;

            transition: .25s ease;

            pointer-events: none;

        }


        .login-input {

            width: 100%;

            height: 54px;

            padding:
                0 16px 0 47px;

            border-radius: 14px;

            border:
                1px solid
                rgba(255,255,255,.10);

            outline: none;

            background:
                rgba(255,255,255,.045);

            color: #fff;

            font-family:
                'Plus Jakarta Sans',
                sans-serif;

            font-size: 14px;

            transition:
                border-color .25s ease,
                box-shadow .25s ease,
                background .25s ease;

        }


        .login-input::placeholder {

            color: #64748b;

        }


        .login-input:hover {

            border-color:
                rgba(168,85,247,.35);

        }


        .login-input:focus {

            border-color: #a855f7;

            background:
                rgba(168,85,247,.06);

            box-shadow:
                0 0 0 4px
                rgba(168,85,247,.10);

        }


        .login-input-wrapper:focus-within
        .login-input-icon {

            color: #c084fc;

        }


        /* =====================================================
           PASSWORD
        ====================================================== */

        .password-toggle {

            position: absolute;

            right: 8px;

            top: 50%;

            transform:
                translateY(-50%);

            width: 40px;

            height: 40px;

            border: none;

            background: transparent;

            color: #64748b;

            border-radius: 10px;

            cursor: pointer;

            transition: .2s ease;

        }


        .password-toggle:hover {

            background:
                rgba(168,85,247,.12);

            color: #c084fc;

        }


        /* =====================================================
           LOGIN BUTTON
        ====================================================== */

        .login-button {

            width: 100%;

            height: 54px;

            border: none;

            border-radius: 14px;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            background:
                linear-gradient(
                    135deg,
                    #06b6d4,
                    #7c3aed
                );

            color: white;

            font-family:
                'Plus Jakarta Sans',
                sans-serif;

            font-weight: 700;

            font-size: 14px;

            cursor: pointer;

            box-shadow:
                0 12px 30px
                rgba(124,58,237,.25);

            transition:
                transform .25s ease,
                box-shadow .25s ease;

        }


        .login-button:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 18px 35px
                rgba(124,58,237,.35);

        }


        .login-button:active {

            transform:
                translateY(0)
                scale(.98);

        }


        .login-button:disabled {

            opacity: .7;

            cursor: wait;

            transform: none;

        }


        /* =====================================================
           REGISTER
        ====================================================== */

        .register-text {

            margin-top: 25px;

            text-align: center;

            color: #64748b;

            font-size: 13px;

        }


        .register-text a {

            color: #c084fc;

            font-weight: 700;

            text-decoration: none;

            transition: .2s ease;

        }


        .register-text a:hover {

            color: #e9d5ff;

            text-decoration: underline;

        }


        /* =====================================================
           BACK
        ====================================================== */

        .login-back {

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 5px;

            margin-top: 18px;

            color: #64748b;

            text-decoration: none;

            font-size: 12px;

            transition: .2s ease;

        }


        .login-back:hover {

            color: #c084fc;

        }


        /* =====================================================
           SECURITY TEXT
        ====================================================== */

        .security-text {

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 6px;

            margin-top: 25px;

            color: #475569;

            font-size: 11px;

        }


        .security-text i {

            color: #22c55e;

        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 900px) {

            .login-container {

                max-width: 550px;

                min-height: auto;

                grid-template-columns: 1fr;

            }


            .login-visual {

                display: none;

            }


            .mobile-logo {

                display: block;

            }


            .login-form-panel {

                padding:
                    45px 35px;

            }

        }


        @media (max-width: 500px) {

            .login-page {

                padding: 20px 14px;

            }


            .login-container {

                border-radius: 22px;

            }


            .login-form-panel {

                padding:
                    35px 22px;

            }


            .login-title {

                font-size: 29px;

            }


            .login-input {

                height: 52px;

            }


            .login-button {

                height: 52px;

            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     BACKGROUND EFFECT
========================================================= -->

<div class="login-bg">

    <div class="login-orb orb-one"></div>

    <div class="login-orb orb-two"></div>

    <div class="login-orb orb-three"></div>

</div>



<!-- =========================================================
     LOGIN PAGE
========================================================= -->

<main class="login-page">

    <div class="login-container">


        <!-- =================================================
             LEFT VISUAL
        ================================================== -->

        <section class="login-visual">


            <!-- DECORATION -->

            <div class="visual-circle circle-one"></div>

            <div class="visual-circle circle-two"></div>



            <div class="visual-content">


                <!-- LOGO -->

                <div class="visual-logo">

                    <div class="visual-logo-icon">

                        <i class="bi bi-mask"></i>

                    </div>


                    <div class="visual-logo-text">

                        COSPLAY<span>RENT</span>

                    </div>

                </div>



                <!-- TITLE -->

                <h2 class="visual-title">

                    WUJUDKAN

                    <br>

                    <span>
                        KARAKTERMU
                    </span>

                </h2>



                <!-- DESCRIPTION -->

                <p class="visual-description">

                    Temukan kostum favoritmu dan
                    tampil percaya diri di setiap event.
                    CosplayRent hadir untuk membuat
                    pengalaman cosplay menjadi lebih mudah.

                </p>



                <!-- FEATURES -->

                <div class="visual-features">


                    <div class="visual-feature">

                        <div class="visual-feature-icon">

                            <i class="bi bi-stars"></i>

                        </div>

                        Koleksi kostum cosplay beragam

                    </div>



                    <div class="visual-feature">

                        <div class="visual-feature-icon">

                            <i class="bi bi-calendar-check"></i>

                        </div>

                        Proses penyewaan mudah

                    </div>



                    <div class="visual-feature">

                        <div class="visual-feature-icon">

                            <i class="bi bi-shield-check"></i>

                        </div>

                        Data akun tersimpan dengan aman

                    </div>


                </div>

            </div>

        </section>



        <!-- =================================================
             FORM LOGIN
        ================================================== -->

        <section class="login-form-panel">


            <div class="login-form-wrapper">


                <!-- MOBILE LOGO -->

                <div class="mobile-logo">

                    COSPLAY<span>RENT</span>

                </div>



                <!-- TITLE -->

                <h1 class="login-title">

                    Selamat <span>Datang!</span>

                </h1>


                <p class="login-subtitle">

                    Masuk ke akun CosplayRent kamu

                </p>



                <!-- ERROR -->

                <?php if ($error !== ""): ?>

                    <div class="login-alert">

                        <i class="bi bi-exclamation-circle"></i>

                        <span>
                            <?= htmlspecialchars($error) ?>
                        </span>

                    </div>

                <?php endif; ?>



                <!-- FORM -->

                <form
                    method="POST"
                    action=""
                    id="loginForm"
                    autocomplete="on"
                >


                    <!-- EMAIL -->

                    <div class="login-group">

                        <label for="email">

                            Email

                        </label>


                        <div class="login-input-wrapper">

                            <i
                                class="
                                    bi
                                    bi-envelope
                                    login-input-icon
                                "
                            ></i>


                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="login-input"
                                value="<?= htmlspecialchars($email) ?>"
                                placeholder="Masukkan email kamu"
                                autocomplete="email"
                                required
                            >

                        </div>

                    </div>



                    <!-- PASSWORD -->

                    <div class="login-group">

                        <label for="password">

                            Password

                        </label>


                        <div class="login-input-wrapper">

                            <i
                                class="
                                    bi
                                    bi-lock
                                    login-input-icon
                                "
                            ></i>


                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="login-input"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                            >


                            <button
                                type="button"
                                id="togglePassword"
                                class="password-toggle"
                                aria-label="Tampilkan password"
                            >

                                <i class="bi bi-eye"></i>

                            </button>

                        </div>

                    </div>



                    <!-- BUTTON -->

                    <button
                        type="submit"
                        class="login-button"
                        id="loginButton"
                    >

                        <i class="bi bi-box-arrow-in-right"></i>

                        <span id="loginButtonText">
                            Login
                        </span>

                    </button>


                </form>



                <!-- REGISTER -->

                <div class="register-text">

                    Belum punya akun?

                    <a href="register.php">

                        Daftar sekarang

                    </a>

                </div>



                <!-- BACK -->

                <a
                    href="index.php"
                    class="login-back"
                >

                    <i class="bi bi-arrow-left"></i>

                    Kembali ke Beranda

                </a>



                <!-- SECURITY -->

                <div class="security-text">

                    <i class="bi bi-shield-lock-fill"></i>

                    Login aman dengan perlindungan password

                </div>

            </div>

        </section>

    </div>

</main>



<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {


        /* =====================================================
           TOGGLE PASSWORD
        ====================================================== */

        const togglePassword =
            document.getElementById(
                "togglePassword"
            );

        const password =
            document.getElementById(
                "password"
            );


        if (
            togglePassword &&
            password
        ) {

            togglePassword.addEventListener(
                "click",
                function () {


                    const isPassword =
                        password.type === "password";


                    password.type =
                        isPassword
                            ? "text"
                            : "password";


                    const icon =
                        this.querySelector("i");


                    if (isPassword) {

                        icon.classList.remove(
                            "bi-eye"
                        );

                        icon.classList.add(
                            "bi-eye-slash"
                        );

                        this.setAttribute(
                            "aria-label",
                            "Sembunyikan password"
                        );

                    } else {

                        icon.classList.remove(
                            "bi-eye-slash"
                        );

                        icon.classList.add(
                            "bi-eye"
                        );

                        this.setAttribute(
                            "aria-label",
                            "Tampilkan password"
                        );

                    }

                }
            );

        }



        /* =====================================================
           LOGIN LOADING
        ====================================================== */

        const loginForm =
            document.getElementById(
                "loginForm"
            );

        const loginButton =
            document.getElementById(
                "loginButton"
            );

        const loginButtonText =
            document.getElementById(
                "loginButtonText"
            );


        if (
            loginForm &&
            loginButton
        ) {

            loginForm.addEventListener(
                "submit",
                function () {


                    if (
                        !loginForm.checkValidity()
                    ) {

                        return;

                    }


                    loginButton.disabled = true;


                    loginButtonText.textContent =
                        "Memproses...";


                    const icon =
                        loginButton.querySelector(
                            "i"
                        );


                    if (icon) {

                        icon.className =
                            "bi bi-arrow-repeat";

                        icon.style.animation =
                            "spin 1s linear infinite";

                    }

                }
            );

        }



        /* =====================================================
           AUTO FOCUS
        ====================================================== */

        const email =
            document.getElementById(
                "email"
            );


        if (
            email &&
            email.value === ""
        ) {

            setTimeout(
                function () {

                    email.focus();

                },
                500
            );

        }

    });


/* =========================================================
   SPINNER
========================================================= */

const style =
    document.createElement("style");

style.textContent = `

@keyframes spin {

    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }

}

`;

document.head.appendChild(style);

</script>


</body>

</html>