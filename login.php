<?php

session_start();

require_once "koneksi.php";


/*
|--------------------------------------------------------------------------
| CEK JIKA SUDAH LOGIN
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['user_id'])) {

    // Jika admin
    if (isset($_SESSION['peran']) && $_SESSION['peran'] === 'admin') {

        header("Location: admin/dashboard.php");
        exit;

    }

    // Jika pelanggan
    header("Location: dashboard.php");
    exit;
}


$error = "";


/*
|--------------------------------------------------------------------------
| PROSES LOGIN
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | VALIDASI INPUT
    |--------------------------------------------------------------------------
    */

    if ($email === "" || $password === "") {

        $error = "Email dan kata sandi wajib diisi.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA PENGGUNA DARI DATABASE
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare(
            "SELECT
                id,
                nama_lengkap,
                email,
                kata_sandi,
                peran
             FROM pengguna
             WHERE email = ?
             LIMIT 1"
        );


        if (!$stmt) {

            $error = "Terjadi kesalahan pada sistem.";

        } else {

            $stmt->bind_param("s", $email);

            $stmt->execute();

            $result = $stmt->get_result();


            /*
            |--------------------------------------------------------------------------
            | CEK USER
            |--------------------------------------------------------------------------
            */

            if ($result->num_rows === 1) {

                $user = $result->fetch_assoc();


                /*
                |--------------------------------------------------------------------------
                | CEK PASSWORD
                |--------------------------------------------------------------------------
                */

                if (password_verify($password, $user["kata_sandi"])) {


                    /*
                    |--------------------------------------------------------------------------
                    | BUAT SESSION BARU
                    |--------------------------------------------------------------------------
                    */

                    session_regenerate_id(true);


                    $_SESSION["user_id"] = $user["id"];

                    $_SESSION["nama"] = $user["nama_lengkap"];

                    $_SESSION["email"] = $user["email"];

                    $_SESSION["peran"] = $user["peran"];


                    /*
                    |--------------------------------------------------------------------------
                    | REDIRECT BERDASARKAN ROLE
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

                    $error = "Email atau kata sandi salah.";

                }

            } else {

                $error = "Email atau kata sandi salah.";

            }


            $stmt->close();

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

    <title>Login - COSPLAYRENT</title>


    <!-- CSS LOGIN -->

    <link
        rel="stylesheet"
        href="assets/css/auth.css"
    >

</head>


<body>


<div class="auth-container">


    <div class="auth-card">


        <!-- LOGO -->

        <div class="logo">
            COSPLAYRENT
        </div>


        <!-- SUBTITLE -->

        <div class="subtitle">
            Masuk untuk mulai menyewa kostum favoritmu
        </div>


        <!-- ERROR -->

        <?php if ($error): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- FORM LOGIN -->

        <form method="POST">


            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">
                    Email
                </label>


                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="nama@email.com"
                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                    required
                >

            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    Kata Sandi
                </label>


                <div class="password-wrapper">


                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        required
                    >


                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword('password', this)"
                    >
                        👁
                    </button>


                </div>

            </div>


            <!-- BUTTON -->

            <button
                type="submit"
                class="btn-submit"
            >
                Masuk Halaman
            </button>


        </form>


        <!-- KEMBALI -->

        <a
            href="index.php"
            class="back-link"
        >
            ← Kembali ke Halaman Utama
        </a>


        <!-- REGISTER -->

        <div class="auth-link">

            Belum punya akun?

            <a href="register.php">
                Daftar
            </a>

        </div>


    </div>

</div>


<!-- JAVASCRIPT -->

<script>

function togglePassword(id, button) {

    const input = document.getElementById(id);

    if (input.type === "password") {

        input.type = "text";

        button.textContent = "🙈";

    } else {

        input.type = "password";

        button.textContent = "👁";

    }

}

</script>


</body>

</html>