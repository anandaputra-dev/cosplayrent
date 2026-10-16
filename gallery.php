
<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';

$sql = "
    SELECT
        k.id,
        k.nama_kostum,
        k.nama_karakter,
        k.nama_serial,
        k.deskripsi,
        k.harga_sewa_per_hari,
        k.status,
        c.nama_kategori AS kategori
    FROM kostum k
    LEFT JOIN kategori c ON c.id = k.kategori_id
    ORDER BY k.id DESC
";

$kostum = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galeri Kostum | COSPLAYRENT</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .gallery-heading { padding: 55px 0 25px; }
        .gallery-heading p { color: #77738b; }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 22px;
            padding: 20px 0 70px;
        }
        .costume-card {
            overflow: hidden;
            border: 1px solid #eee7f7;
            border-radius: 18px;
            background: white;
        }
        .costume-picture {
            height: 250px;
            background: #f1e7fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8b35e8;
            font-size: 52px;
        }
        .costume-content { padding: 20px; }
        .costume-category {
            color: #8b35e8;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .costume-content p {
            color: #77738b;
            line-height: 1.6;
        }
        .costume-price {
            color: #8b35e8;
            font-weight: bold;
            margin: 18px 0;
        }
        .costume-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .status-label { font-size: 12px; color: #555; }
        .empty-gallery {
            padding: 30px;
            background: #f8f4fc;
            border-radius: 15px;
        }
    </style>
</head>
<body>

<header class="header">
    <a class="brand" href="index.php">
        <span class="brand-mark">C</span>
        <span>COSPLAY<span class="brand-accent">RENT</span>
            <small>KARAKTERMU, MOMENMU</small>
        </span>
    </a>

    <div class="nav-actions">
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a class="login-link" href="dashboard.php">Dashboard</a>
            <a class="btn btn-small" href="logout.php">Keluar</a>
        <?php else: ?>
            <a class="login-link" href="login.php">Masuk</a>
            <a class="btn btn-small" href="register.php">Daftar</a>
        <?php endif; ?>
    </div>
</header>

<main class="section-wrap">
    <section class="gallery-heading">
        <span class="eyebrow">TEMUKAN KARAKTER FAVORITMU</span>
        <h1>Galeri Kostum ✦</h1>
        <p>Temukan kostum favoritmu untuk momen spesial.</p>
    </section>

    <?php if (empty($kostum)): ?>
        <div class="empty-gallery">
            Belum ada kostum. Tambahkan data kostum terlebih dahulu ke database.
        </div>
    <?php else: ?>
        <section class="gallery-grid">
            <?php foreach ($kostum as $item): ?>
                <article class="costume-card">
                    <div class="costume-picture">✦</div>

                    <div class="costume-content">
                        <span class="costume-category">
                            <?= e(strtoupper($item['kategori'] ?? 'KOSTUM')) ?>
                        </span>

                        <h3><?= e($item['nama_kostum']) ?></h3>

                        <?php if (!empty($item['nama_karakter'])): ?>
                            <p>Karakter: <?= e($item['nama_karakter']) ?></p>
                        <?php endif; ?>

                        <p>
                            <?= e($item['deskripsi'] ?: 'Kostum cosplay pilihan.') ?>
                        </p>

                        <div class="costume-price">
                            Rp<?= number_format(
                                (float) $item['harga_sewa_per_hari'],
                                0, ',', '.'
                            ) ?> <small>/ hari</small>
                        </div>

                        <div class="costume-actions">
                            <span class="status-label">
                                <?= e(ucfirst($item['status'] ?? 'Perlu konfirmasi')) ?>
                            </span>

                            <a class="btn btn-small"
                               href="detail.php?id=<?= (int) $item['id'] ?>">
                                Detail ↗
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>

</body>
</html>
