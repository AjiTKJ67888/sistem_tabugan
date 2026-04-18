<?php
// ============================================
// HALAMAN LOGOUT
// ============================================

// Mulai session
session_start();

// Hapus semua session
session_destroy();

// Redirect ke login
header('Location: login.php');
exit();
?>
