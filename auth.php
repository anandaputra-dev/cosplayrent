<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
function wajib_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/** Periksa kolom agar halaman tetap berjalan pada database lama. */
function db_has_column(PDO $pdo, string $table, string $column): bool {
    $allowed = ['kostum', 'kategori', 'pengguna'];
    if (!in_array($table, $allowed, true)) return false;
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `" . $table . "` LIKE ?");
    $stmt->execute([$column]);
    return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
}
