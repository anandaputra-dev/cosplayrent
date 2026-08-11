<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

$kota = $_GET['kota'] ?? '';
$kategori = $_GET['kategori'] ?? '';

$sql = "SELECT k.*, cat.nama_kategori, f.foto_url, 'Jakarta Selatan' as lokasi_kota 
        FROM kostum k
        LEFT JOIN kategori cat ON k.kategori_id = cat.id
        LEFT JOIN foto_kostum f ON k.id = f.kostum_id AND f.foto_utama = TRUE
        WHERE 1=1";

if (!empty($kategori)) {
    $sql .= " AND cat.nama_kategori = '" . mysqli_real_escape_string($conn, $kategori) . "'";
}

// Eksekusi query
$result = mysqli_query($conn, $sql);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Simulasi filter kota jika data dinamis
    if (!empty($kota) && strpos(strtolower($row['lokasi_kota']), strtolower($kota)) === false) {
        continue;
    }
    $data[] = $row;
}

echo json_encode($data);
?>