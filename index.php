<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';

$hasFoto = db_has_column($pdo, 'kostum', 'foto');
$hasKota = db_has_column($pdo, 'kostum', 'kota');
$fotoColumn = $hasFoto ? 'k.foto' : 'NULL AS foto';
$kotaColumn = $hasKota ? 'k.kota' : 'NULL AS kota';

$sql = "
    SELECT
        k.id,
        k.nama_kostum,
        k.deskripsi,
        k.nama_karakter,
        k.nama_serial,
        k.harga_sewa_per_hari AS harga_per_hari,
        k.status,
        {$fotoColumn},
        {$kotaColumn},
        c.nama_kategori AS kategori
    FROM kostum k
    LEFT JOIN kategori c ON c.id = k.kategori_id
    ORDER BY k.id DESC
    LIMIT 8
";

$kostum = $pdo->query($sql)->fetchAll();
?>



<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="COSPLAYRENT — Sewa kostum cosplay favoritmu dengan mudah.">
  <title>COSPLAYRENT — Karaktermu, Momenmu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>
  <header class="header">
    <a class="brand" href="#beranda" aria-label="COSPLAYRENT beranda">
      <span class="brand-mark">C</span><span>COSPLAY<span class="brand-accent">RENT</span><small>KARAKTERMU,
          MOMENMU</small></span>
    </a>
    <button class="menu-toggle" id="menuToggle" aria-label="Buka menu" aria-expanded="false">☰</button>
    <nav class="nav" id="nav">
      <a class="active" href="#beranda">Beranda</a>
      <a href="#katalog">Katalog Kostum</a>
      <a href="#cara-sewa">Cara Sewa</a>
      <a href="#tentang">Tentang Kami</a>
    </nav>
    <div class="nav-actions"><?php if (isset($_SESSION['user_id'])): ?>
        <a class="login-link" href="dashboard.php">Akun Saya</a>
        <a class="btn btn-small" href="logout.php">Keluar <span>↗</span></a>
      <?php else: ?>
        <a class="login-link" href="login.php">Masuk</a>
        <a class="btn btn-small" href="register.php">Daftar <span>↗</span></a>
      <?php endif; ?>
    </div>
  </header>

  <main>
    <section class="hero section-wrap" id="beranda">
      <div class="hero-copy">
        <div class="eyebrow"><span class="eyebrow-dot"></span> SETIAP KARAKTER PUNYA CERITA</div>
        <h1>Wujudkan<br><span>Karakter</span><br>Favoritmu<span class="purple-dot">!</span></h1>
        <p class="hero-description">Dari dunia anime ke dunia nyata. Temukan kostum favoritmu dan buat setiap momen jadi
          luar biasa.</p>
        <div class="hero-actions"><a class="btn" href="#katalog">Jelajahi Kostum <span>↗</span></a><a
            class="btn btn-outline" href="#cara-sewa">Cara Sewa <span>→</span></a></div>
        <div class="hero-note"><span class="note-icon">✦</span><span>Untuk acara, sesi foto, dan seru-seruan!</span>
        </div>
      </div>
      <div class="hero-visual">
        <div class="hero-orbit orbit-one"></div>
        <div class="hero-orbit orbit-two"></div>
        <div class="hero-image"><img
            src="assets\img\profilweb1.jpg"
            alt="Suasana acara cosplay dan kostum kreatif"></div>
        <div class="floating-card card-top"><span class="float-icon">✦</span><span><b>Kostum impianmu</b><small>Siap
              menemani momenmu</small></span></div>
        <div class="floating-card card-bottom"><span class="heart">♡</span><span><b>Jadi karakter
              favoritmu</b><small>Tanpa harus membeli kostumnya</small></span></div>
        <span class="sparkle sparkle-one">✦</span><span class="sparkle sparkle-two">✧</span>
      </div>
    </section>

    <section class="benefits section-wrap">
      <div class="benefit-intro"><span class="eyebrow">KARAKTERMU. MOMENMU.</span>
        <h2>Lebih mudah<br>jadi siapa saja.</h2>
      </div>
      <div class="benefit"><span class="benefit-icon">✧</span>
        <div><b>Bersih & terawat</b>
          <p>Nyaman dipakai, siap tampil.</p>
        </div>
      </div>
      <div class="benefit"><span class="benefit-icon">◷</span>
        <div><b>Waktu sewa fleksibel</b>
          <p>Sesuaikan dengan rencanamu.</p>
        </div>
      </div>
      <div class="benefit"><span class="benefit-icon">♢</span>
        <div><b>Lengkap & berkualitas</b>
          <p>Detail kecil, pengalaman besar.</p>
        </div>
      </div>
      <div class="benefit"><span class="benefit-icon">♡</span>
        <div><b>Ramah di kantong</b>
          <p>Jadi karakter tanpa biaya besar.</p>
        </div>
      </div>
    </section>

    <section class="catalog section-wrap" id="katalog">
      <div class="section-heading">
        <div><span class="eyebrow">TEMUKAN DUNIAMU</span>
          <h2>Mau jadi siapa hari ini?</h2>
          <p>Pilih karakter favoritmu dan bersiaplah menciptakan momen seru.</p>
        </div>
        <a class="text-link" href="#katalog">Semua kategori <span>↗</span></a>
      </div>
      <div class="category-list" id="categoryList">
        <button class="category active" data-category="Semua">✦ <span>Semua</span></button>
        <button class="category" data-category="Anime">☄ <span>Anime</span><small>4 kostum</small></button>
        <button class="category" data-category="Game">▣ <span>Game</span><small>1 kostum</small></button>
        <button class="category" data-category="Superhero">⚡ <span>Superhero</span><small>2 kostum</small></button>
        <button class="category" data-category="Fantasy">♧ <span>Fantasy</span><small>1 kostum</small></button>
        <button class="category" data-category="Mitologi">☼ <span>Mitologi</span><small>Segera hadir</small></button>
      </div>
      <div class="product-heading">
        <div><span class="eyebrow">PILIHAN UNTUK MOMEN SPESIALMU</span>
          <h3>Kostum yang bikin kamu bersinar.</h3>
        </div>
        <div class="catalog-tabs"><button class="tab active" data-sort="popular">Paling Populer</button><button
            class="tab" data-sort="newest">Terbaru</button></div>
      </div>
      <div class="product-grid" id="productGrid">
        <?php foreach ($kostum as $item): ?>
          <article class="product-card" data-category="<?= htmlspecialchars($item['kategori']) ?>"
            data-price="<?= (int) $item['harga_per_hari'] ?>" data-popular="<?= (int) $item['id'] ?>">
            <div class="product-image">
              <img src="<?= htmlspecialchars($item['foto'] ?: 'assets/placeholder.svg') ?>"
                alt="<?= htmlspecialchars($item['nama_kostum']) ?>" loading="lazy">
              <span class="product-badge">✦ Koleksi COSPLAYRENT</span>
              <button class="favorite" type="button" aria-label="Simpan favorit">♡</button>
            </div>
            <div class="product-info">
              <div class="product-meta"><span><?= htmlspecialchars(strtoupper($item['kategori'])) ?></span><span
                  class="availability"><i></i> <?= in_array(strtolower((string)($item['status'] ?? '')), ['tersedia', 'ready', 'aktif']) ? 'Tersedia' : ucfirst((string)($item['status'] ?? 'Perlu konfirmasi')) ?></span></div>
              <h4><?= htmlspecialchars($item['nama_kostum']) ?></h4>
              <p><?= htmlspecialchars($item['deskripsi'] ?: 'Kostum cosplay pilihan') ?></p>
              <div class="product-bottom">
                <div class="price">Rp<?= number_format((int) $item['harga_per_hari'], 0, ',', '.') ?> <small>/
                    hari</small>
                </div>
                <a class="add-button" href="detail.php?id=<?= (int) $item['id'] ?>"
                  aria-label="Lihat detail <?= htmlspecialchars($item['nama_kostum']) ?>">↗</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <div style="text-align:center;margin:24px 0 10px"><a class="btn" href="gallery.php">Lihat semua kostum ↗</a></div>
      <p class="catalog-disclaimer">Katalog contoh · Foto merupakan ilustrasi cosplay. Ketersediaan dan detail kostum
        perlu dikonfirmasi sebelum pemesanan.</p>
      <div class="empty-state" id="emptyState" hidden>Belum ada kostum pada kategori ini. Coba pilih kategori lainnya ✨
      </div>
    </section>

    <section class="steps-section" id="cara-sewa">
      <div class="section-wrap">
        <div class="center-heading"><span class="eyebrow">GAMPANG, NGGAK PAKE RIBET</span>
          <h2>Sewa kostum dalam <span>3 langkah</span></h2>
          <p>Lebih sedikit persiapan, lebih banyak keseruan.</p>
        </div>
        <div class="steps-grid">
          <article class="step">
            <div class="step-number">01 <span>↗</span></div>
            <div class="step-icon">⌕</div>
            <h3>Temukan karaktermu</h3>
            <p>Jelajahi katalog dan pilih kostum karakter yang paling kamu suka.</p>
          </article>
          <article class="step">
            <div class="step-number">02 <span>↗</span></div>
            <div class="step-icon">▦</div>
            <h3>Pilih tanggal & pesan</h3>
            <p>Tentukan ukuran, atur tanggal sewa, lalu konfirmasi pesananmu.</p>
          </article>
          <article class="step">
            <div class="step-number">03 <span>↗</span></div>
            <div class="step-icon">✧</div>
            <h3>Siap jadi favoritmu!</h3>
            <p>Selesaikan konfirmasi, ambil kostum, dan nikmati momenmu.</p>
          </article>
        </div>
      </div>
    </section>

    <section class="about section-wrap" id="tentang">
      <div class="about-visual"><img
          src="assets\img\profilweb2.jpg"
          alt="Inspirasi fashion dan kostum kreatif">
        <div class="about-sticker"><span>✦</span><b>Make it<br>your moment.</b></div>
      </div>
      <div class="about-copy"><span class="eyebrow">LEBIH DARI SEKADAR KOSTUM</span>
        <h2>Kamu fokus<br>bersenang-senang.<br><span>Biar kami urus kostumnya.</span></h2>
        <p>Cosplay pertama atau kesekian kalinya, kami ingin membantumu tampil percaya diri. Setiap kostum sebaiknya
          diperiksa dan dibersihkan sebelum menemani petualanganmu.</p><a class="btn" href="gallery.php">Lihat Galeri
          <span>↗</span></a>
        <div class="about-points">
          <div><span>✓</span>
            <p><b>Selalu bersih dan siap pakai</b><small>Perawatan setelah setiap penyewaan.</small></p>
          </div>
          <div><span>✓</span>
            <p><b>Teman cosplay yang bisa diandalkan</b><small>Proses jelas dan mudah dipahami.</small></p>
          </div>
          <div><span>✓</span>
            <p><b>Rencanakan tanpa khawatir</b><small>Konfirmasi ketersediaan sebelum sewa.</small></p>
          </div>
        </div>
      </div>
    </section>

    <section class="community">
      <div class="section-wrap community-inner">
        <div><span class="eyebrow">CERITA DARI SESAMA COSPLAYER</span>
          <h2>Momen mereka,<br><span>karakter mereka.</span></h2>
          <p>Jadilah bagian dari cerita pertama kami. Setelah menyewa, bagikan pengalamanmu dan bantu cosplayer lain
            menemukan karakter favoritnya.</p><a class="btn btn-light" href="#katalog">Temukan kostummu
            <span>↗</span></a>
        </div>
        <div class="community-art"><span class="art-star star-a">✦</span><span class="art-star star-b">✧</span>
          <div class="art-circle"><span>YOUR</span><b>STORY</b><span>STARTS HERE</span></div>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer" id="kontak">
    <div class="section-wrap footer-grid">
      <div class="footer-brand"><a class="brand" href="#beranda"><span class="brand-mark">C</span><span>COSPLAY<span
              class="brand-accent">RENT</span><small>KARAKTERMU, MOMENMU</small></span></a>
        <p>Karakter favoritmu, kini lebih dekat.<br>Teman untuk setiap momen cosplay-mu.</p><span
          class="footer-tag">Dibuat untuk para pecinta cosplay ✦</span>
      </div>
      <div>
        <h4>Jelajahi</h4><a href="#katalog">Katalog Kostum</a><a href="#cara-sewa">Cara Sewa</a><a
          href="#tentang">Tentang Kami</a>
      </div>
      <div>
        <h4>Bantuan</h4><a href="#cara-sewa">Aturan Penyewaan</a><a href="#kontak">Pertanyaan Umum</a><a
          href="#kontak">Pesanan Saya</a>
      </div>
      <div>
        <h4>Mari Terhubung</h4>
        <p>Punya pertanyaan atau ide karakter?</p><a class="contact-link" href="mailto:halo@cosplayrent.example">✉
          Hubungi tim kami</a><a class="contact-link" href="#kontak">◎ Komunitas cosplay</a><span
          class="footer-slogan">Sewa nyaman, tampil percaya diri.</span>
      </div>
    </div>
    <div class="footer-bottom section-wrap"><span>© 2026 COSPLAYRENT. Semua hak dilindungi.</span><a
        href="#beranda">Kembali ke atas ↑</a></div>
  </footer>

  <div class="toast" id="toast" role="status" aria-live="polite"></div>
  <script src="script.js"></script>
</body>

</html>