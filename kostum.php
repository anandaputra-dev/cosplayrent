<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth.php';

wajib_login();
if (($_SESSION['peran'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak. Halaman ini hanya untuk admin.');
}

$uploadDir = __DIR__ . '/uploads/';
$uploadUrl = 'admin/uploads/';
$pesan = '';
$error = '';
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT) ?: 0;

function hapus_foto_lokal(string $daftar, string $folder): void {
    $items = preg_split('/[,;\r\n]+/', $daftar);
    foreach ($items as $item) {
        $item = trim($item);
        if ($item !== '' && strpos($item, 'http://') !== 0 && strpos($item, 'https://') !== 0) {
            $nama = basename($item);
            $path = $folder . $nama;
            if (is_file($path)) @unlink($path);
        }
    }
}

function simpan_foto(array $files, string $folder, string $url, array &$errors): array {
    $hasil = [];
    if (empty($files['name']) || !is_array($files['name'])) return $hasil;
    $jumlah = count($files['name']);
    if ($jumlah > 8) {
        $errors[] = 'Maksimal 8 foto untuk satu kostum.';
        return $hasil;
    }
    $allowed = ['jpg'=>'image/jpeg', 'jpeg'=>'image/jpeg', 'png'=>'image/png', 'webp'=>'image/webp'];
    for ($i = 0; $i < $jumlah; $i++) {
        if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
        if (($files['error'][$i] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $errors[] = 'Ada foto yang gagal diunggah.';
            continue;
        }
        if (($files['size'][$i] ?? 0) > 5 * 1024 * 1024) {
            $errors[] = 'Ukuran setiap foto maksimal 5 MB.';
            continue;
        }
        $tmp = $files['tmp_name'][$i];
        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        $mime = function_exists('mime_content_type') ? mime_content_type($tmp) : '';
        if (!isset($allowed[$ext]) || $mime !== $allowed[$ext]) {
            $errors[] = 'Format foto harus JPG, JPEG, PNG, atau WEBP.';
            continue;
        }
        $nama = bin2hex(random_bytes(12)) . '.' . $ext;
        if (move_uploaded_file($tmp, $folder . $nama)) {
            $hasil[] = $url . $nama;
        } else {
            $errors[] = 'Foto tidak dapat disimpan. Periksa izin folder admin/uploads.';
        }
    }
    return $hasil;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($aksi === 'hapus' && $id > 0) {
        $stmt = $pdo->prepare("SELECT foto FROM kostum WHERE id = ?");
        $stmt->execute([$id]);
        $fotoLama = (string)($stmt->fetchColumn() ?: '');
        $pdo->prepare("DELETE FROM kostum WHERE id = ?")->execute([$id]);
        hapus_foto_lokal($fotoLama, $uploadDir);
        header('Location: kostum.php?status=hapus');
        exit;
    }

    if ($aksi === 'simpan') {
        $nama = trim($_POST['nama_kostum'] ?? '');
        $kategoriId = (int)($_POST['kategori_id'] ?? 0);
        $karakter = trim($_POST['nama_karakter'] ?? '');
        $serial = trim($_POST['nama_serial'] ?? '');
        $ukuran = trim($_POST['ukuran'] ?? '');
        $kota = trim($_POST['kota'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $harga = (int)($_POST['harga_sewa_per_hari'] ?? 0);
        $status = trim($_POST['status'] ?? 'Tersedia');

        if ($nama === '') $error = 'Nama kostum wajib diisi.';
        elseif ($harga < 0) $error = 'Harga tidak boleh kurang dari 0.';
        else {
            $stmt = $pdo->prepare("SELECT foto FROM kostum WHERE id = ?");
            $stmt->execute([$id]);
            $fotoLama = $id > 0 ? (string)($stmt->fetchColumn() ?: '') : '';
            $fotoBaru = simpan_foto($_FILES['foto'] ?? [], $uploadDir, $uploadUrl, $uploadErrors);
            if ($uploadErrors) $error = implode(' ', $uploadErrors);

            if ($error === '') {
                $fotoFinal = array_merge(
                    array_filter(preg_split('/[,;\r\n]+/', $fotoLama), fn($v) => trim($v) !== ''),
                    $fotoBaru
                );
                $fotoFinal = implode(',', array_slice($fotoFinal, 0, 8));

                if ($id > 0) {
                    $sql = "UPDATE kostum SET nama_kostum=?, kategori_id=?, nama_karakter=?, nama_serial=?,
                            ukuran=?, kota=?, deskripsi=?, harga_sewa_per_hari=?, status=?, foto=? WHERE id=?";
                    $pdo->prepare($sql)->execute([
                        $nama, $kategoriId ?: null, $karakter, $serial, $ukuran, $kota,
                        $deskripsi, $harga, $status, $fotoFinal, $id
                    ]);
                } else {
                    $sql = "INSERT INTO kostum
                        (nama_kostum, kategori_id, nama_karakter, nama_serial, ukuran, kota,
                         deskripsi, harga_sewa_per_hari, status, foto)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $pdo->prepare($sql)->execute([
                        $nama, $kategoriId ?: null, $karakter, $serial, $ukuran, $kota,
                        $deskripsi, $harga, $status, $fotoFinal
                    ]);
                }
                header('Location: kostum.php?status=simpan');
                exit;
            }
        }
    }
}

if (isset($_GET['status'])) {
    $pesan = $_GET['status'] === 'hapus' ? 'Data kostum berhasil dihapus.' : 'Data kostum berhasil disimpan.';
}
$kategori = $pdo->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori")->fetchAll();
$edit = null;
if ($editId) {
    $stmt = $pdo->prepare("SELECT * FROM kostum WHERE id = ?");
    $stmt->execute([$editId]);
    $edit = $stmt->fetch();
}
$daftar = $pdo->query("SELECT k.id, k.nama_kostum, k.foto, k.harga_sewa_per_hari, k.status,
                              c.nama_kategori
                       FROM kostum k LEFT JOIN kategori c ON c.id = k.kategori_id
                       ORDER BY k.id DESC")->fetchAll();
function foto_admin(string $foto): string {
    $list = array_values(array_filter(array_map('trim', preg_split('/[,;\r\n]+/', $foto))));
    if (!$list) return '../assets/placeholder.svg';
    $path = $list[0];
    if (preg_match('~^https?://~i', $path)) return $path;
    return '../' . ltrim($path, '/');
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kelola Kostum | COSPLAYRENT</title>
<link rel="stylesheet" href="../assets/style.css">
<style>
.admin-wrap{padding:42px 0 75px}.admin-intro{padding:27px 30px;background:linear-gradient(135deg,#8b35e8,#b879f5);color:#fff;border-radius:18px;margin-bottom:25px}.admin-intro p{margin-bottom:0;line-height:1.6}
.admin-panel{background:#fff;border:1px solid #eee7f7;border-radius:16px;padding:24px;margin-bottom:24px}.admin-form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.admin-form label{font-size:12px;font-weight:700;color:#615775;display:block;margin-bottom:6px}.admin-form input,.admin-form select,.admin-form textarea{width:100%;box-sizing:border-box;padding:12px;border:1px solid #ddd2ee;border-radius:9px;background:#fff;color:#292238;font:inherit}.admin-form textarea{min-height:90px;resize:vertical}.full{grid-column:1/-1}.help{font-size:11px;color:#888;margin-top:6px}.notice{padding:12px 15px;border-radius:9px;background:#eaf9f0;color:#216e45;margin:15px 0}.error{background:#fff0f0;color:#a12626}.admin-table-wrap{overflow-x:auto}.admin-table{width:100%;border-collapse:collapse;min-width:650px}.admin-table th,.admin-table td{text-align:left;padding:12px;border-bottom:1px solid #eee9f4;font-size:12px}.admin-table th{color:#746987;background:#faf7ff}.admin-thumb{width:55px;height:65px;object-fit:cover;border-radius:8px;background:#f1e7fa}.actions{display:flex;gap:7px;align-items:center}.actions a,.actions button{border:0;border-radius:7px;padding:8px 10px;font-size:11px;font-weight:700;cursor:pointer}.edit-link{background:#f1e7fa;color:#7131b4}.delete-btn{background:#fff0f0;color:#a12626}.file-input{background:#faf7ff!important}@media(max-width:650px){.admin-form{grid-template-columns:1fr}.full{grid-column:auto}.admin-panel{padding:17px}}
</style>
</head>
<body>
<header class="header">
<a class="brand" href="../index.php"><span class="brand-mark">C</span><span>COSPLAY<span class="brand-accent">RENT</span><small>KARAKTERMU, MOMENMU</small></span></a>
<div class="nav-actions"><a class="login-link" href="../dashboard.php">Dashboard</a><a class="btn btn-small" href="../logout.php">Keluar</a></div>
</header>
<main class="section-wrap admin-wrap">
<section class="admin-intro"><span>✦ AREA ADMIN</span><h1>Kelola Galeri Kostum</h1><p>Tambah kostum dan unggah foto langsung dari halaman ini tanpa mengedit kode.</p></section>
<?php if ($pesan): ?><div class="notice"><?= e($pesan) ?></div><?php endif; ?>
<?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
<section class="admin-panel" id="form-kostum">
<h2><?= $edit ? 'Edit Kostum' : 'Tambah Kostum Baru' ?></h2>
<form method="post" enctype="multipart/form-data" class="admin-form">
<input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<div><label>Nama Kostum *</label><input name="nama_kostum" required value="<?= e($edit['nama_kostum'] ?? '') ?>" placeholder="Contoh: Naruto Uzumaki"></div>
<div><label>Kategori</label><select name="kategori_id"><option value="">Pilih kategori</option><?php foreach($kategori as $k): ?><option value="<?= (int)$k['id'] ?>" <?= (string)($edit['kategori_id'] ?? '') === (string)$k['id'] ? 'selected' : '' ?>><?= e($k['nama_kategori']) ?></option><?php endforeach; ?></select></div>
<div><label>Nama Karakter</label><input name="nama_karakter" value="<?= e($edit['nama_karakter'] ?? '') ?>"></div>
<div><label>Nama Anime / Serial / Game</label><input name="nama_serial" value="<?= e($edit['nama_serial'] ?? '') ?>"></div>
<div><label>Ukuran</label><input name="ukuran" value="<?= e($edit['ukuran'] ?? '') ?>" placeholder="S / M / L / XL"></div>
<div><label>Kota</label><input name="kota" value="<?= e($edit['kota'] ?? '') ?>" placeholder="Contoh: Malang"></div>
<div><label>Harga Sewa per Hari (Rp)</label><input type="number" min="0" name="harga_sewa_per_hari" value="<?= e((string)($edit['harga_sewa_per_hari'] ?? '0')) ?>"></div>
<div><label>Status</label><select name="status"><?php foreach(['Tersedia','Disewa','Perlu konfirmasi'] as $st): ?><option <?= ($edit['status'] ?? 'Tersedia') === $st ? 'selected' : '' ?>><?= e($st) ?></option><?php endforeach; ?></select></div>
<div class="full"><label>Deskripsi</label><textarea name="deskripsi"><?= e($edit['deskripsi'] ?? '') ?></textarea></div>
<div class="full"><label>Foto Kostum (boleh pilih beberapa sekaligus)</label><input class="file-input" type="file" name="foto[]" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple><div class="help">Format JPG, PNG, WEBP. Maksimal 8 foto per kostum, 5 MB per foto. Saat edit, foto baru akan ditambahkan ke foto lama.</div>
<?php if (!empty($edit['foto'])): ?><div class="help">Foto yang sudah tersimpan: <?= e($edit['foto']) ?></div><?php endif; ?></div>
<div class="full"><button class="btn" type="submit">Simpan Kostum</button> <?php if($edit): ?><a class="btn btn-outline" href="kostum.php">Batal Edit</a><?php endif; ?></div>
</form>
</section>
<section class="admin-panel"><h2>Daftar Kostum</h2><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Foto</th><th>Nama Kostum</th><th>Kategori</th><th>Harga / Hari</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($daftar as $item): ?><tr><td><img class="admin-thumb" src="<?= e(foto_admin((string)($item['foto'] ?? ''))) ?>" onerror="this.src='../assets/placeholder.svg'" alt=""></td><td><?= e($item['nama_kostum']) ?></td><td><?= e($item['nama_kategori'] ?? '-') ?></td><td>Rp<?= number_format((int)$item['harga_sewa_per_hari'],0,',','.') ?></td><td><?= e($item['status'] ?? '-') ?></td><td><div class="actions"><a class="edit-link" href="kostum.php?edit=<?= (int)$item['id'] ?>#form-kostum">Edit</a><form method="post" onsubmit="return confirm('Yakin ingin menghapus kostum ini?')"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="delete-btn" type="submit">Hapus</button></form></div></td></tr><?php endforeach; ?>
<?php if(!$daftar): ?><tr><td colspan="6">Belum ada data kostum.</td></tr><?php endif; ?>
</tbody></table></div></section>
</main>
</body></html>
