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

            $resultCheck =
                $stmtCheck->get_result();


            /*
            |--------------------------------------------------------------------------
            | Email sudah terdaftar
            |--------------------------------------------------------------------------
            */

            if ($resultCheck->num_rows > 0) {

                $error =
                    "Email tersebut sudah terdaftar.";

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

                $stmtInsert =
                    $conn->prepare($sqlInsert);


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

                        /*
                        | Kosongkan form
                        */

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
<html lang="id" data-bs-theme="dark">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">


    <title>Daftar - CosplayRent</title>


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


    <!-- CSS -->

    <link rel="stylesheet"
          href="style.css">

</head>


<body>


<div class="auth-page">

    <div class="auth-card register-card">


        <!-- LOGO -->

        <div class="auth-logo">
            Cosplay<span>Rent</span>
        </div>


        <!-- TITLE -->

        <h1 class="auth-title">
            Buat Akun
        </h1>


        <!-- SUBTITLE -->

        <p class="auth-subtitle">
            Daftar untuk mulai menggunakan CosplayRent
        </p>


        <!-- ERROR -->

        <?php if ($error !== ""): ?>

            <div class="auth-alert">

                <i class="bi bi-exclamation-circle me-2"></i>

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- SUCCESS -->

        <?php if ($success !== ""): ?>

            <div class="auth-alert">

                <i class="bi bi-check-circle me-2"></i>

                <?= htmlspecialchars($success) ?>

            </div>

        <?php endif; ?>


        <!-- FORM -->

        <form
            method="POST"
            action=""
            class="auth-form"
            autocomplete="on">


            <!-- NAMA -->

            <div class="auth-group">

                <label for="nama">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="<?= htmlspecialchars($nama) ?>"
                    placeholder="Masukkan nama lengkap"
                    autocomplete="name"
                    required
                >

            </div>


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
                    placeholder="Masukkan email"
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
                        placeholder="Minimal 6 karakter"
                        autocomplete="new-password"
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


            <!-- KONFIRMASI PASSWORD -->

            <div class="auth-group">

                <label for="password_confirm">
                    Konfirmasi Password
                </label>


                <div class="auth-password">

                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        placeholder="Ulangi password"
                        autocomplete="new-password"
                        required
                    >


                    <button
                        type="button"
                        id="togglePasswordConfirm"
                        aria-label="Tampilkan konfirmasi password">

                        <i class="bi bi-eye"></i>

                    </button>

                </div>

            </div>


            <!-- ROLE -->

            <div class="auth-role">

                <div class="auth-role-title">

                    Jenis Akun

                </div>


                <div class="auth-radio">


                    <label>

                        <input
                            type="radio"
                            name="is_owner"
                            value="Tidak"

                            <?= $is_owner === "Tidak"
                                ? "checked"
                                : "" ?>

                            required
                        >

                        <span>
                            Pelanggan
                        </span>

                    </label>


                    <label>

                        <input
                            type="radio"
                            name="is_owner"
                            value="Iya"

                            <?= $is_owner === "Iya"
                                ? "checked"
                                : "" ?>
                        >

                        <span>
                            Owner / Admin
                        </span>

                    </label>


                </div>

            </div>


            <!-- BUTTON -->

            <button
                type="submit"
                class="auth-button">

                <i class="bi bi-person-plus me-2"></i>

                Daftar

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

function setupPasswordToggle(buttonId, inputId) {

    const button =
        document.getElementById(buttonId);

    const input =
        document.getElementById(inputId);


    if (!button || !input) {
        return;
    }


    button.addEventListener("click", function () {

        const isPassword =
            input.type === "password";


        input.type =
            isPassword ? "text" : "password";


        const icon =
            this.querySelector("i");


        if (isPassword) {

            icon.classList.remove("bi-eye");

            icon.classList.add("bi-eye-slash");

        } else {

            icon.classList.remove("bi-eye-slash");

            icon.classList.add("bi-eye");

        }

    });

}


/*
|--------------------------------------------------------------------------
| Password
|--------------------------------------------------------------------------
*/

setupPasswordToggle(
    "togglePassword",
    "password"
);


/*
|--------------------------------------------------------------------------
| Confirm Password
|--------------------------------------------------------------------------
*/

setupPasswordToggle(
    "togglePasswordConfirm",
    "password_confirm"
);

</script>


</body>
</html>