<?php

require_once "koneksi.php";

/*
|--------------------------------------------------------------------------
| Jika user sudah login
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['user_id'])) {

    if (isset($_SESSION['peran']) && $_SESSION['peran'] === 'admin') {
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

                if (password_verify($password, $user["kata_sandi"])) {

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
                    $_SESSION["nama"] = $user["nama_lengkap"];
                    $_SESSION["email"] = $user["email"];
                    $_SESSION["peran"] = $user["peran"];


                    /*
                    |--------------------------------------------------------------------------
                    | Redirect berdasarkan role
                    |--------------------------------------------------------------------------
                    */

                    if ($user["peran"] === "admin") {

                        header("Location: admin/dashboard.php");
                        exit;

                    } else {

                        header("Location: dashboard.php");
                        exit;
                    }

                } else {

                    $error = "Email atau password salah.";
                }

            } else {

                $error = "Email atau password salah.";
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

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - CosplayRent</title>


    <!-- Google Font -->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- CSS Utama -->

    <link rel="stylesheet"
          href="style.css">

</head>


<body>


<div class="auth-page">

    <div class="auth-card">


        <!-- LOGO -->

        <div class="auth-logo">
            Cosplay<span>Rent</span>
        </div>


        <!-- TITLE -->

        <h1 class="auth-title">
            Selamat Datang
        </h1>


        <!-- SUBTITLE -->

        <p class="auth-subtitle">
            Masuk ke akun CosplayRent kamu
        </p>


        <!-- ERROR -->

        <?php if ($error !== ""): ?>

            <div class="auth-alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <!-- FORM LOGIN -->

        <form method="POST"
              action=""
              class="auth-form"
              autocomplete="on">


            <!-- EMAIL -->

            <div class="auth-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email) ?>"
                    placeholder="Masukkan email kamu"
                    autocomplete="email"
                    required
                >

            </div>


            <!-- PASSWORD -->

            <div class="auth-group">

                <label for="password">
                    Password
                </label>


                <div class="auth-password">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >


                    <button
                        type="button"
                        id="togglePassword"
                        aria-label="Tampilkan password">

                        <i class="bi bi-eye"></i>

                    </button>

                </div>

            </div>


            <!-- BUTTON -->

            <button
                type="submit"
                class="auth-button">

                <i class="bi bi-box-arrow-in-right me-2"></i>

                Login

            </button>


        </form>


        <!-- REGISTER -->

        <div class="auth-footer">

            Belum punya akun?

            <a href="register.php">
                Daftar sekarang
            </a>

        </div>


        <!-- BACK -->

        <a href="index.php"
           class="auth-back">

            <i class="bi bi-arrow-left me-1"></i>

            Kembali ke Beranda

        </a>


    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| Toggle Password
|--------------------------------------------------------------------------
*/

const togglePassword =
    document.getElementById("togglePassword");

const password =
    document.getElementById("password");


if (togglePassword && password) {

    togglePassword.addEventListener("click", function () {

        const isPassword =
            password.type === "password";


        password.type =
            isPassword ? "text" : "password";


        const icon =
            this.querySelector("i");


        if (isPassword) {

            icon.classList.remove("bi-eye");

            icon.classList.add("bi-eye-slash");

            this.setAttribute(
                "aria-label",
                "Sembunyikan password"
            );

        } else {

            icon.classList.remove("bi-eye-slash");

            icon.classList.add("bi-eye");

            this.setAttribute(
                "aria-label",
                "Tampilkan password"
            );
        }

    });

}

</script>


</body>
</html>