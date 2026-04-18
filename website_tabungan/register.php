<?php
// ============================================
// HALAMAN REGISTRASI
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

// Proses form registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = validate_input($_POST['username'] ?? '');
    $password = validate_input($_POST['password'] ?? '');
    $confirm_password = validate_input($_POST['confirm_password'] ?? '');
    
    // Panggil fungsi register
    $result = register_user($koneksi, $username, $password, $confirm_password);
    
    if ($result['success']) {
        $message = $result['message'];
        $message_type = 'success';
        // Redirect ke login setelah 2 detik
        header('Refresh: 2; url=login.php');
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
    <title>Registrasi - Sistem Tabungan</title>
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
                <h1>Daftar Akun</h1>
                <p>Buat akun baru untuk memulai tabungan Anda</p>
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

            <!-- Form Registrasi -->
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
                        placeholder="Masukkan username (minimal 3 karakter)"
                        minlength="3"
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
                        placeholder="Masukkan password (minimal 6 karakter)"
                        minlength="6"
                        required
                    >
                    <small>Minimal 6 karakter</small>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i> Konfirmasi Password
                    </label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Ketik ulang password Anda"
                        minlength="6"
                        required
                    >
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-large">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>
            </form>

            <!-- Link ke Login -->
            <div class="auth-footer">
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>

        <!-- Decoration -->
        <div class="auth-decoration">
            <div class="decoration-circle decoration-circle-1"></div>
            <div class="decoration-circle decoration-circle-2"></div>
        </div>
    </div>

    <script src="script.js?v=1.2"></script>
    <script>
        // Validasi password cocok saat user mengetik
        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password && confirmPassword && password !== confirmPassword) {
                document.getElementById('confirm_password').classList.add('error');
            } else {
                document.getElementById('confirm_password').classList.remove('error');
            }
        }

        document.getElementById('confirm_password').addEventListener('input', validatePasswordMatch);
    </script>
</body>
</html>
