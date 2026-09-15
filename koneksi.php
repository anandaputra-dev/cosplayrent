<?php

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi database
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "rental_kostum"
);

// Cek koneksi
if (!$conn) {
    die(
        "Koneksi database gagal: " .
        mysqli_connect_error()
    );
}