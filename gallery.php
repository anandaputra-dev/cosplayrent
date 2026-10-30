<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';

// Filter kota dan kategori menggunakan parameter GET.
$kotaDipilih = trim((string)($_GET['kota'] ?? ''));
$kategoriDipilih = trim((string)($_GET['kategori'] ?? ''));
$cari = trim((string)($_GET['cari'] ?? ''));

$hasKota = db_has_column($pdo, 'kostum', 'kota');
$hasFoto = db_has_column($pdo, 'kostum', 'foto');
$kotaList = ['Bandung', 'Bekasi', 'Bogor', 'Depok', 'Jakarta', 'Malang', 'Medan', 'Palembang', 'Semarang', 'Sidoarjo', 'Surabaya', 'Tangerang', 'Yogyakarta'];
if ($hasKota) {
    $kotaDb = $pdo->query("SELECT DISTINCT kota FROM kostum WHERE kota IS NOT NULL AND kota <> '' ORDER BY kota")
        ->fetchAll(PDO::FETCH_COLUMN);
    $kotaList = array_values(array_unique(array_merge($kotaList, $kotaDb)));
    natcasesort($kotaList);
    $kotaList = array_values($kotaList);
}
$kategoriList = $pdo->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori")
    ->fetchAll(PDO::FETCH_ASSOC);
$fotoSelect = $hasFoto ? 'k.foto' : 'NULL AS foto';
$kotaSelect = $hasKota ? 'k.kota' : 'NULL AS kota';
$sql = "SELECT k.id, k.nama_kostum, k.nama_karakter, k.nama_serial, k.ukuran,
               k.deskripsi, k.harga_sewa_per_hari, k.status, {$kotaSelect}, {$fotoSelect},
               c.nama_kategori AS kategori
        FROM kostum k
        LEFT JOIN kategori c ON c.id = k.kategori_id
        WHERE 1=1";
$params = [];
if ($kotaDipilih !== '' && $hasKota) { $sql .= " AND k.kota = ?"; $params[] = $kotaDipilih; }
if ($kategoriDipilih !== '' && ctype_digit($kategoriDipilih)) { $sql .= " AND k.kategori_id = ?"; $params[] = (int)$kategoriDipilih; }
if ($cari !== '') {
    $sql .= " AND (k.nama_kostum LIKE ? OR k.nama_karakter LIKE ? OR k.nama_serial LIKE ?)";
    $term = '%' . $cari . '%'; array_push($params, $term, $term, $term);
}
$sql .= " ORDER BY k.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$kostum = $stmt->fetchAll(PDO::FETCH_ASSOC);
function fotoKostum(?string $foto): string {
    $foto = trim((string)$foto);
    if ($foto === '') return 'assets/placeholder.svg';
    // Terima URL http(s) atau path lokal relatif; hindari skema URL lain.
    if (preg_match('~^https?://~i', $foto)) return $foto;
    return ltrim($foto, '/');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Galeri Kostum | COSPLAYRENT</title><link rel="stylesheet" href="assets/style.css">
<style>
.gallery-heading{padding:55px 0 25px}.gallery-heading p{color:#77738b}.gallery-filter{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;padding:20px;background:#faf7ff;border:1px solid #eee7f7;border-radius:18px}.gallery-filter label{display:block;font-size:12px;font-weight:700;color:#615775;margin-bottom:7px}.gallery-filter input,.gallery-filter select{width:100%;box-sizing:border-box;padding:12px;border:1px solid #ddd2ee;border-radius:10px;background:#fff;color:#292238}.filter-actions{display:flex;align-items:end;gap:8px;flex-wrap:wrap}.filter-reset{padding:12px 16px;border-radius:10px;text-decoration:none;border:1px solid #d8c7ee;color:#6f2fc0;background:#fff}.gallery-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:22px;padding:25px 0 70px}.costume-card{overflow:hidden;border:1px solid #eee7f7;border-radius:18px;background:#fff}.costume-picture{height:250px;background:#f1e7fa;display:flex;align-items:center;justify-content:center;color:#8b35e8;font-size:52px;overflow:hidden}.costume-picture img{width:100%;height:100%;object-fit:cover}.costume-content{padding:20px}.costume-category{color:#8b35e8;font-size:11px;font-weight:bold;letter-spacing:1px}.costume-content p{color:#77738b;line-height:1.6}.costume-price{color:#8b35e8;font-weight:bold;margin:18px 0}.costume-actions{display:flex;align-items:center;justify-content:space-between;gap:10px}.status-label{font-size:12px;color:#555}.empty-gallery{padding:30px;background:#f8f4fc;border-radius:15px}.city-label{display:inline-block;margin-top:4px;padding:5px 9px;border-radius:20px;background:#f1e7fa;color:#7131b4;font-size:12px}
</style></head><body>
<header class="header"><a class="brand" href="index.php"><span class="brand-mark">C</span><span>COSPLAY<span class="brand-accent">RENT</span><small>KARAKTERMU, MOMENMU</small></span></a><div class="nav-actions"><?php if (!empty($_SESSION['user_id'])): ?><a class="login-link" href="dashboard.php">Dashboard</a><a class="btn btn-small" href="logout.php">Keluar</a><?php else: ?><a class="login-link" href="login.php">Masuk</a><a class="btn btn-small" href="register.php">Daftar</a><?php endif; ?></div></header>
<main class="section-wrap"><section class="gallery-heading"><span class="eyebrow">TEMUKAN KARAKTER FAVORITMU</span><h1>Galeri Kostum ✦</h1><p>Pilih kostum berdasarkan kota, kategori, atau nama karakter.</p></section>
<form class="gallery-filter" method="get" action="gallery.php">
<div><label for="cari">Cari kostum</label><input id="cari" name="cari" type="search" placeholder="Nama kostum/karakter" value="<?= e($cari) ?>"></div>
<div><label for="kota">Kota</label><select id="kota" name="kota"><option value="">Semua kota</option><?php foreach($kotaList as $kota): ?><option value="<?= e($kota) ?>" <?= $kotaDipilih === $kota ? 'selected' : '' ?>><?= e($kota) ?></option><?php endforeach; ?></select></div>
<div><label for="kategori">Kategori</label><select id="kategori" name="kategori"><option value="">Semua kategori</option><?php foreach($kategoriList as $kat): ?><option value="<?= (int)$kat['id'] ?>" <?= $kategoriDipilih === (string)$kat['id'] ? 'selected' : '' ?>><?= e($kat['nama_kategori']) ?></option><?php endforeach; ?></select></div>
<div class="filter-actions"><button class="btn btn-small" type="submit">Terapkan filter</button><a class="filter-reset" href="gallery.php">Reset</a></div>
</form>
<?php if ($kotaDipilih !== '' && !$hasKota): ?><p class="empty-gallery">Filter kota belum bisa digunakan karena kolom <code>kota</code> belum ada di database. Jalankan file <code>database_gallery_kota.sql</code> terlebih dahulu.</p><?php endif; ?><p style="margin-top:20px;color:#77738b"><?= count($kostum) ?> kostum ditemukan<?= $kotaDipilih !== '' ? ' di ' . e($kotaDipilih) : '' ?>.</p>
<?php if (!$kostum): ?><div class="empty-gallery">Belum ada kostum yang cocok dengan filter ini. Coba pilih kota atau kategori lain.</div><?php else: ?><section class="gallery-grid">
<?php foreach($kostum as $item): ?><article class="costume-card"><div class="costume-picture"><img src="<?= e(fotoKostum($item['foto'] ?? null)) ?>" alt="<?= e($item['nama_kostum']) ?>" loading="lazy" onerror="this.onerror=null;this.src='assets/placeholder.svg'"></div><div class="costume-content"><span class="costume-category"><?= e(strtoupper($item['kategori'] ?? 'KOSTUM')) ?></span><h3><?= e($item['nama_kostum']) ?></h3><?php if(!empty($item['nama_karakter'])): ?><p>Karakter: <?= e($item['nama_karakter']) ?></p><?php endif; ?><?php if(!empty($item['nama_serial'])): ?><p>Serial: <?= e($item['nama_serial']) ?></p><?php endif; ?><p><?= e($item['deskripsi'] ?: 'Kostum cosplay pilihan.') ?></p><?php if(!empty($item['kota'])): ?><span class="city-label">⌖ <?= e($item['kota']) ?></span><?php endif; ?><div class="costume-price">Rp<?= number_format((float)$item['harga_sewa_per_hari'],0,',','.') ?> <small>/ hari</small></div><div class="costume-actions"><span class="status-label"><?= e(ucfirst($item['status'] ?? 'Perlu konfirmasi')) ?></span><a class="btn btn-small" href="detail.php?id=<?= (int)$item['id'] ?>">Detail ↗</a></div></div></article><?php endforeach; ?>
</section><?php endif; ?></main></body></html>
