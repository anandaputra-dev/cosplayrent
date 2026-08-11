<?php
require_once 'koneksi.php';

$message = '';
if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query  = "SELECT * FROM pengguna WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['kata_sandi'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama_lengkap'];
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Kata sandi salah!";
        }
    } else {
        $message = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - CosplayRent</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

<div class="container" style="max-width: 450px;">
    <div class="glass-card p-4 p-md-5">
        <div class="text-center mb-4">
            <a href="index.php" class="text-decoration-none">
                <h3 class="fw-bold tracking-wider mb-2">COSPLAY<span style="color:var(--neon-purple)">RENT</span></h3>
            </a>
            <p class="text-secondary small">Masuk untuk mulai menyewa kostum favoritmu</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger py-2 small" role="alert">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-secondary small">Email</label>
                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-secondary small">Kata Sandi</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="btn btn-neon w-100 py-2 mb-3">Masuk Halaman</button>
            <div class="text-center">
                <a href="index.php" class="text-secondary small text-decoration-none">← Kembali ke Halaman Utama</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Menyelaraskan mode tema sesuai pilihan terakhir
    const savedTheme = localStorage.getItem('cosplayrent_theme') || 'dark';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
</script>
</body>
</html>