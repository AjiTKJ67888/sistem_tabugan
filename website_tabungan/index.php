<?php
// ============================================
// INDEX.PHP - HALAMAN UTAMA (AUTO REDIRECT)
// ============================================

// Mulai session
session_start();

// Cek apakah user sudah login
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    // Jika sudah login, redirect ke dashboard
    header('Location: dashboard.php');
} else {
    // Jika belum login, redirect ke login
    header('Location: login.php');
}

exit();
?>
