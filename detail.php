<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: gallery.php'); exit; }

// Ambil data berdasarkan kolom yang tersedia pada database.
$hasFoto = db_has_column($pdo, 'kostum', 'foto');
$hasKota = db_has_column($pdo, 'kostum', 'kota');
$selectFoto = $hasFoto ? 'k.foto' : 'NULL AS foto';
$selectKota = $hasKota ? 'k.kota' : 'NULL AS kota';

$stmt = $pdo->prepare("SELECT k.id, k.nama_kostum, k.nama_karakter, k.nama_serial,
    k.ukuran, k.deskripsi, k.harga_sewa_per_hari, k.status,
    {$selectFoto}, {$selectKota}, c.nama_kategori AS kategori
    FROM kostum k
    LEFT JOIN kategori c ON c.id = k.kategori_id
    WHERE k.id = ? LIMIT 1");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) { http_response_code(404); exit('Kostum tidak ditemukan.'); }

// Kolom foto tunggal dapat berisi satu path atau beberapa path dipisahkan koma/baris baru.
$rawPhotos = trim((string)($item['foto'] ?? ''));
$photos = $rawPhotos !== '' ? preg_split('/[,;\r\n]+/', $rawPhotos) : [];
$photos = array_values(array_filter(array_map('trim', $photos), fn($v) => $v !== ''));
if (!$photos) $photos = ['assets/placeholder.svg'];
$photos = array_slice($photos, 0, 8);
$firstPhoto = $photos[0];
function detail_photo_url(string $photo): string {
    // Hanya izinkan path relatif lokal atau URL http/https.
    if (preg_match('~^https?://~i', $photo)) return $photo;
    return ltrim($photo, '/');
}
$harga = (float)($item['harga_sewa_per_hari'] ?? 0);
$status = trim((string)($item['status'] ?? 'Perlu konfirmasi'));
$tersedia = in_array(strtolower($status), ['tersedia', 'ready', 'aktif'], true);
$ukuran = trim((string)($item['ukuran'] ?? ''));
$deskripsi = trim((string)($item['deskripsi'] ?? ''));
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($item['nama_kostum']) ?> | COSPLAYRENT</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
<style>
.detail-page{padding:38px 0 76px}
.detail-layout{display:grid;grid-template-columns:minmax(280px, .95fr) minmax(320px,1.25fr) minmax(220px,.62fr);gap:28px;align-items:start}
.detail-gallery{min-width:0}
.detail-main-photo{height: min( min(66vw, 650px), 650px);min-height:420px;position:relative;border-radius:7px;overflow:hidden;background:linear-gradient(145deg,#f1e7ff,#e9f4ff);border:1px solid var(--line);display:grid;place-items:center}
.detail-main-photo img{width:100%;height:100%;object-fit:contain;display:block}
.detail-photo-nav{position:absolute;top:50%;transform:translateY(-50%);width:42px;height:42px;border:0;border-radius:50%;background:rgba(23,19,43,.72);color:#fff;font-size:27px;line-height:1;display:grid;place-items:center;z-index:2;cursor:pointer;transition:background .2s,transform .2s}
.detail-photo-nav:hover{background:var(--purple);transform:translateY(-50%) scale(1.06)}
.detail-photo-prev{left:14px}.detail-photo-next{right:14px}
.detail-thumbs{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px;margin-top:10px}
.detail-thumb{padding:0;height:88px;border:2px solid transparent;border-radius:8px;overflow:hidden;background:#f4edff;cursor:pointer}
.detail-thumb.active,.detail-thumb:focus{border-color:var(--purple);outline:none}
.detail-thumb img{width:100%;height:100%;object-fit:cover;display:block}
.detail-thumb:hover{border-color:#c7a2ef}
@media(max-width:1050px){.detail-main-photo{height: min(62vw,560px);min-height:380px}}
@media(max-width:700px){.detail-main-photo{height: min(112vw,520px);min-height:300px}.detail-photo-nav{width:38px;height:38px}.detail-thumb{height:75px}}
.detail-info{min-width:0}
.detail-info h1{font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(26px,3vw,36px);line-height:1.2;letter-spacing:-1.2px;margin:9px 0 8px;color:var(--ink)}
.detail-price{font-family:'Plus Jakarta Sans',sans-serif;color:var(--purple);font-size:25px;font-weight:800;margin:10px 0 13px}
.detail-price small{font-family:'DM Sans',sans-serif;font-size:13px;color:var(--muted);font-weight:500}
.detail-chips{display:flex;gap:7px;flex-wrap:wrap;margin:12px 0 20px}
.detail-chip{font-size:11px;font-weight:700;color:#6c35a5;background:#f4edff;border:1px solid #e8dafa;border-radius:7px;padding:6px 10px}
.detail-facts{display:grid;gap:11px;margin:18px 0 20px}
.detail-fact{display:flex;gap:10px;align-items:flex-start;color:var(--ink);font-size:14px;line-height:1.5}
.detail-fact span:first-child{width:20px;flex:0 0 20px;color:var(--purple);text-align:center}
.detail-actions{display:flex;gap:10px;flex-wrap:wrap;margin:18px 0 28px}
.detail-actions .btn{justify-content:center}
.detail-section{border-top:1px solid var(--line);padding-top:18px;margin-top:18px}
.detail-section h2{font-family:'Plus Jakarta Sans',sans-serif;font-size:20px;margin:0 0 12px}
.detail-section h3{font-size:14px;margin:18px 0 9px;color:var(--ink)}
.detail-section p,.detail-section li{font-size:13px;line-height:1.8;color:var(--muted)}
.detail-section ul{padding-left:20px;margin:8px 0}
.detail-side{border:1px solid var(--line);border-radius:15px;background:#fff;box-shadow:0 12px 35px #3520500b;overflow:hidden}
.detail-side-top{padding:18px}
.detail-side-brand{display:flex;align-items:center;gap:11px;margin-bottom:13px}
.detail-side-logo{width:43px;height:43px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(140deg,#a855f7,#6d28d9);color:white;font-weight:800;font-size:20px}
.detail-side-brand b{display:block;font-size:13px;color:var(--purple)}
.detail-side-brand small{font-size:11px;color:var(--muted)}
.detail-side p{font-size:13px;line-height:1.7;color:var(--muted);margin:8px 0}
.detail-side-bottom{padding:15px 18px;border-top:1px solid var(--line);background:#fcfbff}
.detail-side-bottom .btn{width:100%;justify-content:center}
.status-label{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;padding:6px 9px;border-radius:7px;background:#f1fbf5;color:#258653}
.status-label.unavailable{background:#fff5ed;color:#a65b20}
.detail-back{display:inline-flex;margin-bottom:18px;color:var(--purple);font-size:12px;font-weight:700}
@media(max-width:1050px){.detail-layout{grid-template-columns:minmax(260px,.9fr) minmax(300px,1.1fr)}.detail-side{grid-column:2}.detail-main-photo{height:430px}}
@media(max-width:700px){.detail-page{padding-top:22px}.detail-layout{grid-template-columns:1fr;gap:22px}.detail-main-photo{height:min(105vw,430px)}.detail-side{grid-column:auto}.detail-info h1{font-size:28px}.detail-thumb{height:75px}}
</style>
</head>
<body>
<header class="header">
 <a class="brand" href="index.php"><span class="brand-mark">C</span><span>COSPLAY<span class="brand-accent">RENT</span><small>KARAKTERMU, MOMENMU</small></span></a>
 <a class="btn btn-small" href="gallery.php">← Kembali ke galeri</a>
</header>
<main class="section-wrap detail-page">
<a class="detail-back" href="gallery.php">← Kembali ke semua kostum</a>
<div class="detail-layout">
 <section class="detail-gallery" aria-label="Galeri foto kostum">
  <div class="detail-main-photo">
   <img id="mainCostumePhoto" src="<?= e(detail_photo_url($firstPhoto)) ?>" alt="<?= e($item['nama_kostum']) ?>" onerror="this.onerror=null;this.src='assets/placeholder.svg'">
   <?php if (count($photos) > 1): ?>
    <button class="detail-photo-nav detail-photo-prev" type="button" id="prevCostumePhoto" aria-label="Foto sebelumnya">‹</button>
    <button class="detail-photo-nav detail-photo-next" type="button" id="nextCostumePhoto" aria-label="Foto berikutnya">›</button>
   <?php endif; ?>
  </div>
  <?php if (count($photos) > 1): ?>
  <div class="detail-thumbs">
   <?php foreach ($photos as $index => $photo): ?>
   <button class="detail-thumb <?= $index === 0 ? 'active' : '' ?>" type="button" data-photo="<?= e(detail_photo_url($photo)) ?>" aria-label="Lihat foto <?= $index + 1 ?>">
    <img src="<?= e(detail_photo_url($photo)) ?>" alt="Foto <?= $index + 1 ?>" onerror="this.src='assets/placeholder.svg'">
   </button>
   <?php endforeach; ?>
  </div>
  <?php endif; ?>
 </section>

 <section class="detail-info">
  <span class="eyebrow"><?= e(strtoupper($item['kategori'] ?? 'KOLEKSI KOSTUM')) ?></span>
  <h1><?= e($item['nama_kostum']) ?></h1>
  <div class="detail-price">Rp<?= number_format($harga,0,',','.') ?> <small>/ hari</small></div>
  <div class="detail-chips">
   <?php if ($ukuran !== ''): ?><span class="detail-chip">Ukuran <?= e($ukuran) ?></span><?php endif; ?>
   <?php if (!empty($item['nama_karakter'])): ?><span class="detail-chip"><?= e($item['nama_karakter']) ?></span><?php endif; ?>
   <?php if (!empty($item['nama_serial'])): ?><span class="detail-chip"><?= e($item['nama_serial']) ?></span><?php endif; ?>
  </div>
  <div class="detail-facts">
   <?php if (!empty($item['kota'])): ?><div class="detail-fact"><span>⌖</span><span><?= e($item['kota']) ?></span></div><?php endif; ?>
   <div class="detail-fact"><span>◇</span><span><?= e($item['kategori'] ?? 'Kategori belum ditentukan') ?></span></div>
   <div class="detail-fact"><span>◷</span><span><span class="status-label <?= $tersedia ? '' : 'unavailable' ?>"><?= e(ucfirst($status)) ?></span></span></div>
  </div>
  <div class="detail-actions">
   <?php if (!empty($_SESSION['user_id'])): ?>
    <a class="btn" href="mailto:halo@cosplayrent.example?subject=Pertanyaan%20sewa%20<?= rawurlencode($item['nama_kostum']) ?>">Tanyakan penyewaan ↗</a>
   <?php else: ?>
    <a class="btn" href="login.php">Masuk untuk menyewa ↗</a>
   <?php endif; ?>
   <a class="btn btn-outline" href="gallery.php">Lihat kostum lain</a>
  </div>
  <div class="detail-section">
   <h2>Detail Kostum</h2>
   <h3>✦ Tentang kostum</h3>
   <p><?= nl2br(e($deskripsi !== '' ? $deskripsi : 'Belum ada deskripsi untuk kostum ini. Silakan hubungi pengelola untuk informasi lebih lanjut.')) ?></p>
   <?php if ($ukuran !== ''): ?><h3>▱ Informasi ukuran</h3><ul><li>Ukuran kostum: <?= e($ukuran) ?></li></ul><?php endif; ?>
   <h3>✧ Informasi penyewaan</h3>
   <ul><li>Harga sewa: Rp<?= number_format($harga,0,',','.') ?> per hari.</li><li>Pastikan status dan ketersediaan kostum dikonfirmasi sebelum melakukan penyewaan.</li></ul>
  </div>
 </section>

 <aside class="detail-side">
  <div class="detail-side-top">
   <div class="detail-side-brand"><div class="detail-side-logo">C</div><div><b>COSPLAYRENT</b><small>Informasi penyewaan kostum</small></div></div>
   <p>Temukan karakter favoritmu dan persiapkan momen cosplay yang menyenangkan.</p>
   <p>✦ Informasi kostum ditampilkan berdasarkan data katalog.</p>
   <?php if (!empty($item['kota'])): ?><p>⌖ Lokasi kostum: <?= e($item['kota']) ?></p><?php endif; ?>
  </div>
  <div class="detail-side-bottom"><a class="btn btn-outline" href="gallery.php">Jelajahi galeri</a></div>
 </aside>
</div>
</main>
<script>
(function () {
    const main = document.getElementById('mainCostumePhoto');
    const thumbs = Array.from(document.querySelectorAll('.detail-thumb'));
    if (!main || thumbs.length === 0) return;

    let activeIndex = 0;

    function showPhoto(index) {
        activeIndex = (index + thumbs.length) % thumbs.length;
        const selected = thumbs[activeIndex];
        main.src = selected.dataset.photo;
        thumbs.forEach((thumb, i) => {
            thumb.classList.toggle('active', i === activeIndex);
            thumb.setAttribute('aria-pressed', i === activeIndex ? 'true' : 'false');
        });
    }

    thumbs.forEach((button, index) => {
        button.addEventListener('click', () => showPhoto(index));
    });

    const previous = document.getElementById('prevCostumePhoto');
    const next = document.getElementById('nextCostumePhoto');
    if (previous) previous.addEventListener('click', () => showPhoto(activeIndex - 1));
    if (next) next.addEventListener('click', () => showPhoto(activeIndex + 1));
})();
</script>
</body>
</html>
