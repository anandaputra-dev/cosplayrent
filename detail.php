
<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        k.*,
        c.nama_kategori AS kategori
    FROM kostum k
    LEFT JOIN kategori c ON c.id = k.kategori_id
    WHERE k.id = ?
    LIMIT 1
");

$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    http_response_code(404);
    exit('Kostum tidak ditemukan.');
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($item['nama_kostum']) ?> | COSPLAYRENT</title>
    <link rel="stylesheet" href="assets/style.css">

    <style>
        .detail {
            padding: 60px 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            min-height: 65vh;
        }

        .detail img {
            width: 100%;
            height: 440px;
            object-fit: contain;
            background: #f1e7fa;
            border-radius: 18px;
        }

        .detail p {
            color: #77738b;
            line-height: 1.8;
        }

        @media (max-width: 700px) {
            .detail {
                grid-template-columns: 1fr;
            }

            .detail img {
                height: 320px;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <a class="brand" href="index.php">
            <span class="brand-mark">C</span>
            <span>
                COSPLAY<span class="brand-accent">RENT</span>
                <small>KARAKTERMU, MOMENMU</small>
            </span>
        </a>

        <a class="btn btn-small" href="gallery.php">← Galeri</a>
    </header>

    <main class="section-wrap detail">
        <img
            src="assets/placeholder.svg"
            alt="<?= e($item['nama_kostum']) ?>"
        >

        <div>
            <span class="eyebrow">
                <?= e(strtoupper($item['kategori'] ?? 'KOSTUM')) ?>
            </span>

            <h1><?= e($item['nama_kostum']) ?></h1>

            <?php if (!empty($item['nama_karakter'])): ?>
                <p>Karakter: <?= e($item['nama_karakter']) ?></p>
            <?php endif; ?>

            <?php if (!empty($item['nama_serial'])): ?>
                <p>Serial: <?= e($item['nama_serial']) ?></p>
            <?php endif; ?>

            <p>
                <?= e($item['deskripsi'] ?: 'Kostum cosplay pilihan.') ?>
            </p>

            <h2 style="color:#8b35e8">
                Rp<?= number_format((float) $item['harga_sewa_per_hari'], 0, ',', '.') ?>
                <small style="font-size:14px;color:#77738b">/ hari</small>
            </h2>

            <p>
                Status:
                <?= e(ucfirst($item['status'] ?? 'Perlu konfirmasi')) ?>.
                Konfirmasikan ketersediaan kepada pengelola sebelum menyewa.
            </p>

            <?php if (!empty($_SESSION['user_id'])): ?>
                <a class="btn"
                   href="mailto:halo@cosplayrent.example?subject=Pertanyaan%20sewa%20<?= rawurlencode($item['nama_kostum']) ?>">
                    Tanyakan penyewaan ↗
                </a>
            <?php else: ?>
                <a class="btn" href="login.php">
                    Masuk untuk melanjutkan ↗
                </a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
