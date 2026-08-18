<?php
function getKostumById(mysqli $conn, int $id): ?array
{
    $stmt = mysqli_prepare($conn, "SELECT k.*, cat.nama_kategori, f.foto_url 
        FROM kostum k
        LEFT JOIN kategori cat ON k.kategori_id = cat.id
        LEFT JOIN foto_kostum f ON k.id = f.kostum_id AND f.foto_utama = TRUE
        WHERE k.id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    return $row ?: null;
}

/**
 * Hitung jumlah hari sewa dari tanggal_mulai s/d tanggal_selesai (inklusif).
 * Minimal 1 hari.
 */
function hitungJumlahHari(string $tanggal_mulai, string $tanggal_selesai): int
{
    $mulai   = new DateTime($tanggal_mulai);
    $selesai = new DateTime($tanggal_selesai);
    $selisih = $mulai->diff($selesai)->days + 1;
    return max(1, $selisih);
}

/**
 * Hitung total biaya sewa = harga per hari x jumlah hari.
 */
function hitungTotalBiaya(float $harga_per_hari, int $jumlah_hari): float
{
    return $harga_per_hari * $jumlah_hari;
}

/**
 * Cek apakah kostum sudah dibooking orang lain pada rentang tanggal yang
 * beririsan dengan tanggal yang diminta. Hanya menghitung penyewaan yang
 * masih aktif (belum selesai/dibatalkan).
 * Return true jika TERSEDIA (tidak bentrok), false jika bentrok.
 */
function cekKetersediaanKostum(mysqli $conn, int $kostum_id, string $tanggal_mulai, string $tanggal_selesai, ?int $kecuali_penyewaan_id = null): bool
{
    $sql = "SELECT dp.id 
            FROM detail_penyewaan dp
            JOIN penyewaan p ON dp.penyewaan_id = p.id
            WHERE dp.kostum_id = ?
              AND p.status_sewa NOT IN ('selesai', 'dibatalkan')
              AND p.tanggal_mulai <= ?
              AND p.tanggal_selesai >= ?";

    $params = [$kostum_id, $tanggal_selesai, $tanggal_mulai];
    $types  = "iss";

    if ($kecuali_penyewaan_id !== null) {
        $sql .= " AND p.id != ?";
        $params[] = $kecuali_penyewaan_id;
        $types .= "i";
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_num_rows($res) === 0; // true = tidak ada bentrok = tersedia
}

/**
 * Simpan header penyewaan baru. Mengembalikan ID penyewaan yang baru dibuat.
 */
function buatPenyewaan(mysqli $conn, int $pengguna_id, string $tanggal_mulai, string $tanggal_selesai, float $total_biaya): int
{
    $stmt = mysqli_prepare($conn, "INSERT INTO penyewaan 
        (pengguna_id, tanggal_mulai, tanggal_selesai, total_biaya_sewa, status_pembayaran, status_sewa) 
        VALUES (?, ?, ?, ?, 'belum_bayar', 'menunggu_konfirmasi')");
    mysqli_stmt_bind_param($stmt, "issd", $pengguna_id, $tanggal_mulai, $tanggal_selesai, $total_biaya);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($conn);
}

/**
 * Simpan satu baris detail item kostum yang disewa dalam sebuah penyewaan.
 */
function tambahDetailPenyewaan(mysqli $conn, int $penyewaan_id, int $kostum_id, float $harga_per_hari, int $jumlah_hari, float $subtotal): int
{
    $stmt = mysqli_prepare($conn, "INSERT INTO detail_penyewaan 
        (penyewaan_id, kostum_id, harga_sewa_per_hari, jumlah_hari, subtotal) 
        VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iidid", $penyewaan_id, $kostum_id, $harga_per_hari, $jumlah_hari, $subtotal);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($conn);
}

/**
 * Ubah status kostum (mis. jadi 'disewa' saat masa sewa berlangsung).
 */
function updateStatusKostum(mysqli $conn, int $kostum_id, string $status): void
{
    $stmt = mysqli_prepare($conn, "UPDATE kostum SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $kostum_id);
    mysqli_stmt_execute($stmt);
}

/**
 * Ambil data penyewaan lengkap (header + item) untuk halaman konfirmasi.
 */
function getPenyewaanById(mysqli $conn, int $penyewaan_id, int $pengguna_id): ?array
{
    $stmt = mysqli_prepare($conn, "SELECT * FROM penyewaan WHERE id = ? AND pengguna_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $penyewaan_id, $pengguna_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $penyewaan = mysqli_fetch_assoc($res);
    if (!$penyewaan) {
        return null;
    }

    $stmtItem = mysqli_prepare($conn, "SELECT dp.*, k.nama_kostum, k.nama_karakter 
        FROM detail_penyewaan dp 
        JOIN kostum k ON dp.kostum_id = k.id 
        WHERE dp.penyewaan_id = ?");
    mysqli_stmt_bind_param($stmtItem, "i", $penyewaan_id);
    mysqli_stmt_execute($stmtItem);
    $resItem = mysqli_stmt_get_result($stmtItem);

    $items = [];
    while ($row = mysqli_fetch_assoc($resItem)) {
        $items[] = $row;
    }

    $penyewaan['items'] = $items;
    return $penyewaan;
}