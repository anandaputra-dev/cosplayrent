
<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';

// Mengambil filter dari URL
$kotaDipilih = trim((string) ($_GET['kota'] ?? ''));
$kategoriDipilih = trim((string) ($_GET['kategori'] ?? ''));
$cari = trim((string) ($_GET['cari'] ?? ''));

// Memeriksa kolom database
$hasKota = db_has_column($pdo, 'kostum', 'kota');
$hasFoto = db_has_column($pdo, 'kostum', 'foto');

// Daftar kota
$kotaList = [
    'Bandung', 'Bekasi', 'Bogor', 'Depok',
    'Jakarta', 'Malang', 'Medan', 'Palembang',
    'Semarang', 'Sidoarjo', 'Surabaya',
    'Tangerang', 'Yogyakarta'
];

if ($hasKota) {
    $kotaDb = $pdo->query("
        SELECT DISTINCT kota
        FROM kostum
        WHERE kota IS NOT NULL AND kota <> ''
        ORDER BY kota
    ")->fetchAll(PDO::FETCH_COLUMN);

    $kotaList = array_values(
        array_unique(array_merge($kotaList, $kotaDb))
    );

    natcasesort($kotaList);
    $kotaList = array_values($kotaList);
}

// Mengambil kategori
$kategoriList = $pdo->query("
    SELECT id, nama_kategori
    FROM kategori
    ORDER BY nama_kategori
")->fetchAll(PDO::FETCH_ASSOC);

// Menyesuaikan kolom foto dan kota
$fotoSelect = $hasFoto ? 'k.foto' : 'NULL AS foto';
$kotaSelect = $hasKota ? 'k.kota' : 'NULL AS kota';

// Query utama
$sql = "
    SELECT
        k.id,
        k.nama_kostum,
        k.nama_karakter,
        k.nama_serial,
        k.ukuran,
        k.deskripsi,
        k.harga_sewa_per_hari,
        k.status,
        {$kotaSelect},
        {$fotoSelect},
        c.nama_kategori AS kategori
    FROM kostum k
    LEFT JOIN kategori c ON c.id = k.kategori_id
    WHERE 1=1
";

$params = [];

// Filter kota
if ($kotaDipilih !== '' && $hasKota) {
    $sql .= " AND k.kota = ?";
    $params[] = $kotaDipilih;
}

// Filter kategori
if ($kategoriDipilih !== '' && ctype_digit($kategoriDipilih)) {
    $sql .= " AND k.kategori_id = ?";
    $params[] = (int) $kategoriDipilih;
}

// Pencarian kostum
if ($cari !== '') {
    $sql .= "
        AND (
            k.nama_kostum LIKE ?
            OR k.nama_karakter LIKE ?
            OR k.nama_serial LIKE ?
        )
    ";

    $term = '%' . $cari . '%';

    array_push($params, $term, $term, $term);
}

$sql .= " ORDER BY k.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$kostum = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Fungsi mengambil foto pertama dari database
function fotoKostum(?string $foto): ?string
{
    $foto = trim((string) $foto);

    if ($foto === '') {
        return null;
    }

    // Mendukung banyak foto yang dipisahkan koma,
    // titik koma, atau baris baru.
    $daftarFoto = preg_split('/[,;\r\n]+/', $foto);

    $foto = trim($daftarFoto[0] ?? '');

    if ($foto === '') {
        return null;
    }

    // URL gambar eksternal
    if (preg_match('~^https?://~i', $foto)) {
        return $foto;
    }

    $foto = str_replace('\\', '/', $foto);
    $foto = preg_replace('~^(\./)+~', '', $foto);
    $foto = ltrim($foto, '/');

    // Jika hanya nama file, tambahkan folder upload.
    if (!str_contains($foto, '/')) {
        $foto = 'admin/uploads/' . $foto;
    }

    return $foto;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Galeri Kostum | COSPLAYRENT</title>

    <link rel="stylesheet" href="assets/style.css">

    <style>
        /* HEADING GALERI */
        .gallery-heading {
            padding: 55px 0 30px;
        }

        .gallery-heading h1 {
            font-size: clamp(30px, 4vw, 42px);
            margin: 18px 0 12px;
            font-weight: 800;
        }

        .gallery-heading p {
            color: #77738b;
            font-size: 16px;
        }

        /* FILTER */
        .gallery-filter {
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr auto;
            gap: 14px;
            align-items: end;
            padding: 22px;
            background: #faf7ff;
            border: 1px solid #eee7f7;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(95, 40, 150, .05);
        }

        .gallery-filter label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #615775;
            margin-bottom: 8px;
        }

        .gallery-filter input,
        .gallery-filter select {
            width: 100%;
            box-sizing: border-box;
            height: 46px;
            padding: 0 14px;
            border: 1px solid #ddd2ee;
            border-radius: 12px;
            background: white;
            color: #292238;
            outline: none;
            transition: .2s;
        }

        .gallery-filter input:focus,
        .gallery-filter select:focus {
            border-color: #9139e9;
            box-shadow: 0 0 0 3px rgba(145, 57, 233, .12);
        }

        .filter-actions {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .filter-actions .btn,
        .filter-reset {
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 17px;
            border-radius: 12px;
            white-space: nowrap;
        }

        .filter-reset {
            text-decoration: none;
            border: 1px solid #d8c7ee;
            color: #7131b4;
            background: white;
        }

        .filter-reset:hover {
            background: #f1e7fa;
        }

        /* JUMLAH KOSTUM */
        .result-info {
            margin-top: 25px;
            color: #77738b;
            font-size: 15px;
        }

        /* GRID GALERI */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
            padding: 25px 0 75px;
        }

        /* CARD KOSTUM */
        .costume-card {
            overflow: hidden;
            border: 1px solid #eee7f7;
            border-radius: 19px;
            background: white;
            transition: transform .25s, box-shadow .25s;
        }

        .costume-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 15px 35px rgba(83, 39, 130, .13);
        }

        /* FOTO KOSTUM */
        .costume-picture {
            position: relative;
            height: 245px;
            background: linear-gradient(135deg, #f1e7fa, #e8d6fa);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .costume-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s;
        }

        .costume-card:hover .costume-picture img {
            transform: scale(1.06);
        }

        .costume-picture.no-photo {
            color: #9139e9;
            font-size: 45px;
        }

        /* ISI CARD */
        .costume-content {
            padding: 20px;
        }

        .costume-category {
            display: inline-block;
            color: #9139e9;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.2px;
            margin-bottom: 9px;
        }

        .costume-content h3 {
            color: #21152f;
            font-size: 18px;
            line-height: 1.4;
            margin: 0 0 10px;
            min-height: 50px;
        }

        .costume-content p {
            color: #77738b;
            font-size: 13px;
            line-height: 1.6;
            margin: 7px 0;
        }

        .costume-description {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 42px;
        }

        /* KOTA */
        .city-label {
            display: inline-block;
            margin-top: 9px;
            padding: 6px 11px;
            border-radius: 20px;
            background: #f1e7fa;
            color: #7131b4;
            font-size: 12px;
        }

        /* HARGA */
        .costume-price {
            color: #8b35e8;
            font-size: 18px;
            font-weight: 800;
            margin: 18px 0;
        }

        .costume-price small {
            font-size: 12px;
            font-weight: 400;
            color: #77738b;
        }

        /* STATUS DAN TOMBOL */
        .costume-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-top: 1px solid #f0eaf6;
            padding-top: 15px;
        }

        .status-label {
            font-size: 12px;
            font-weight: 700;
            color: #25844d;
        }

        .status-label.unavailable {
            color: #d34c55;
        }

        /* KOSONG */
        .empty-gallery {
            padding: 35px;
            text-align: center;
            background: #faf7ff;
            border: 1px dashed #d8c7ee;
            border-radius: 18px;
            color: #77738b;
        }

        /* RESPONSIVE */
        @media (max-width: 1050px) {
            .gallery-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .gallery-filter {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {
            .gallery-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 13px;
            }

            .costume-picture {
                height: 190px;
            }

            .costume-content {
                padding: 14px;
            }

            .costume-content h3 {
                font-size: 15px;
            }

            .gallery-filter {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                width: 100%;
            }

            .filter-actions > * {
                flex: 1;
            }
        }

        @media (max-width: 420px) {
            .gallery-grid {
                grid-template-columns: 1fr;
            }

            .costume-picture {
                height: 250px;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header class="header">

        <a class="brand" href="index.php">
            <span class="brand-mark">C</span>

            <span>
                COSPLAY<span class="brand-accent">RENT</span>
                <small>KARAKTERMU, MOMENMU</small>
            </span>
        </a>

        <div class="nav-actions">

            <?php if (!empty($_SESSION['user_id'])): ?>

                <a class="login-link" href="dashboard.php">
                    Dashboard
                </a>

                <a class="btn btn-small" href="logout.php">
                    Keluar
                </a>

            <?php else: ?>

                <a class="login-link" href="login.php">
                    Masuk
                </a>

                <a class="btn btn-small" href="register.php">
                    Daftar
                </a>

            <?php endif; ?>

        </div>
    </header>


    <!-- KONTEN GALERI -->
    <main class="section-wrap">

        <!-- JUDUL -->
        <section class="gallery-heading">

            <span class="eyebrow">
                TEMUKAN KARAKTER FAVORITMU
            </span>

            <h1>Galeri Kostum ✦</h1>

            <p>
                Pilih kostum berdasarkan kota, kategori, atau nama karakter.
            </p>

        </section>


        <!-- FILTER -->
        <form class="gallery-filter" method="get" action="gallery.php">

            <div>
                <label for="cari">Cari kostum</label>

                <input
                    id="cari"
                    name="cari"
                    type="search"
                    placeholder="Cari nama kostum atau karakter..."
                    value="<?= e($cari) ?>"
                >
            </div>


            <div>
                <label for="kota">Kota</label>

                <select id="kota" name="kota">

                    <option value="">Semua kota</option>

                    <?php foreach ($kotaList as $kota): ?>

                        <option
                            value="<?= e($kota) ?>"
                            <?= $kotaDipilih === $kota ? 'selected' : '' ?>
                        >
                            <?= e($kota) ?>
                        </option>

                    <?php endforeach; ?>

                </select>
            </div>


            <div>
                <label for="kategori">Kategori</label>

                <select id="kategori" name="kategori">

                    <option value="">Semua kategori</option>

                    <?php foreach ($kategoriList as $kat): ?>

                        <option
                            value="<?= (int) $kat['id'] ?>"
                            <?= $kategoriDipilih === (string) $kat['id'] ? 'selected' : '' ?>
                        >
                            <?= e($kat['nama_kategori']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>
            </div>


            <div class="filter-actions">

                <button class="btn btn-small" type="submit">
                    Terapkan Filter
                </button>

                <a class="filter-reset" href="gallery.php">
                    Reset
                </a>

            </div>

        </form>


        <!-- PESAN JIKA KOLOM KOTA BELUM ADA -->
        <?php if ($kotaDipilih !== '' && !$hasKota): ?>

            <div class="empty-gallery">
                Filter kota belum bisa digunakan karena kolom
                <strong>kota</strong> belum tersedia di database.
            </div>

        <?php endif; ?>


        <!-- JUMLAH KOSTUM -->
        <p class="result-info">
            Menampilkan
            <strong><?= count($kostum) ?></strong>
            kostum ditemukan
            <?= $kotaDipilih !== '' ? 'di ' . e($kotaDipilih) : '' ?>.
        </p>


        <!-- HASIL GALERI -->
        <?php if (!$kostum): ?>

            <div class="empty-gallery">
                <h3>✦ Kostum tidak ditemukan</h3>

                <p>
                    Belum ada kostum yang cocok dengan pencarianmu.
                    Coba gunakan filter atau kata kunci lain.
                </p>

                <a class="btn btn-small" href="gallery.php">
                    Lihat Semua Kostum
                </a>
            </div>

        <?php else: ?>

            <section class="gallery-grid">

                <?php foreach ($kostum as $item): ?>

                    <?php
                    $fotoUrl = fotoKostum($item['foto'] ?? null);

                    $status = strtolower(
                        trim((string) ($item['status'] ?? 'tersedia'))
                    );

                    $tidakTersedia = in_array(
                        $status,
                        ['disewa', 'tidak tersedia', 'nonaktif'],
                        true
                    );
                    ?>

                    <article class="costume-card">

                        <!-- FOTO -->
                        <div class="costume-picture <?= !$fotoUrl ? 'no-photo' : '' ?>">

                            <?php if ($fotoUrl): ?>

                                <img
                                    src="<?= e($fotoUrl) ?>"
                                    alt="<?= e($item['nama_kostum']) ?>"
                                    loading="lazy"
                                    onerror="this.style.display='none';this.parentElement.classList.add('no-photo');"
                                >

                            <?php else: ?>

                                ✦

                            <?php endif; ?>

                        </div>


                        <!-- INFORMASI KOSTUM -->
                        <div class="costume-content">

                            <span class="costume-category">
                                <?= e(strtoupper($item['kategori'] ?? 'KOSTUM')) ?>
                            </span>


                            <h3>
                                <?= e($item['nama_kostum']) ?>
                            </h3>


                            <?php if (!empty($item['nama_karakter'])): ?>

                                <p>
                                    Karakter:
                                    <?= e($item['nama_karakter']) ?>
                                </p>

                            <?php endif; ?>


                            <?php if (!empty($item['nama_serial'])): ?>

                                <p>
                                    Serial:
                                    <?= e($item['nama_serial']) ?>
                                </p>

                            <?php endif; ?>


                            <p class="costume-description">
                                <?= e($item['deskripsi'] ?: 'Kostum cosplay pilihan.') ?>
                            </p>


                            <?php if (!empty($item['kota'])): ?>

                                <span class="city-label">
                                    ⌖ <?= e($item['kota']) ?>
                                </span>

                            <?php endif; ?>


                            <!-- HARGA -->
                            <div class="costume-price">

                                Rp<?= number_format(
                                    (float) $item['harga_sewa_per_hari'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                                <small>/ hari</small>

                            </div>


                            <!-- STATUS DAN DETAIL -->
                            <div class="costume-actions">

                                <span class="status-label <?= $tidakTersedia ? 'unavailable' : '' ?>">

                                    <?= e(ucfirst($item['status'] ?? 'Perlu konfirmasi')) ?>

                                </span>


                                <a
                                    class="btn btn-small"
                                    href="detail.php?id=<?= (int) $item['id'] ?>"
                                >
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
