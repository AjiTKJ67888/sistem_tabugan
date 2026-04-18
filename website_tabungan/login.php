<?php
// ============================================
// HALAMAN LOGIN
// ============================================

// Mulai session
session_start();

// Hubungkan ke database
require_once 'koneksi.php';
require_once 'functions.php';

// Jika sudah login, redirect ke dashboard
if (is_logged_in()) {
    redirect('dashboard.php');
}

// Variable untuk menyimpan pesan
$message = '';
$message_type = '';

// Proses form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = validate_input($_POST['username'] ?? '');
    $password = validate_input($_POST['password'] ?? '');
    
    // Panggil fungsi login
    $result = login_user($koneksi, $username, $password);
    
    if ($result['success']) {
        $message = $result['message'];
        $message_type = 'success';
        // Redirect ke dashboard setelah 1 detik
        header('Refresh: 1; url=dashboard.php');
    } else {
        $message = $result['message'];
        $message_type = 'error';
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Tabungan</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body">
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <i class="fas fa-piggy-bank"></i>
                <span>SakuKu</span>
            </div>
            <div class="navbar-user">
                <button id="themeToggle" class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo / Title -->
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <h1>Masuk ke Akun</h1>
                <p>Kelola tabungan Anda dengan mudah dan aman</p>
            </div>

            <!-- Notifikasi -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible" id="alert">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <span><?php echo escape_output($message); ?></span>
                    <button type="button" onclick="closeAlert()" class="alert-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form method="POST" class="auth-form">
                <!-- Username -->
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Masukkan username Anda"
                        required
                    >
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Masukkan password Anda"
                        required
                    >
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-large">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <!-- Link ke Register -->
            <div class="auth-footer">
                <p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
            </div>
        </div>

        <!-- Decoration -->
        <div class="auth-decoration">
            <div class="decoration-circle decoration-circle-1"></div>
            <div class="decoration-circle decoration-circle-2"></div>
        </div>
    </div>

    <script src="script.js?v=1.2"></script>
</body>
</html>
