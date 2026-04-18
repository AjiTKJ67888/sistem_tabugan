<?php
// ============================================
// FILE KONEKSI DATABASE
// ============================================

// Konfigurasi database untuk Laragon
$host = 'localhost';
$username = 'root';
$password = ''; // Laragon default password kosong
$database = 'sistem_tabungan';

// Buat koneksi dengan MySQLi procedural
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

// Set charset ke UTF-8
mysqli_set_charset($koneksi, 'utf8mb4');

// Variable global untuk koneksi database
// Dapat diakses di file lain yang melakukan include koneksi.php
?>
