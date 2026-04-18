<?php
// ============================================
// HALAMAN PROFILE & SETTINGS
// ============================================

session_start();

require_once 'koneksi.php';
require_once 'functions.php';

// Cek login
if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user = get_user_by_id($koneksi, $user_id);

$message = '';
$message_type = '';

// Handle change password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    header('Content-Type: application/json');
    
    $old_password = validate_input($_POST['old_password'] ?? '');
    $new_password = validate_input($_POST['new_password'] ?? '');
    $confirm_password = validate_input($_POST['confirm_password'] ?? '');
    
    // Validasi
    if (strlen($new_password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter']);
        exit();
    }
    
    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Password tidak cocok']);
        exit();
    }
    
    // Ambil password lama dari database
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = $result->fetch_assoc();
    
    // Verifikasi password lama
    if (!password_verify($old_password, $user_data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Password lama salah']);
        exit();
    }
    
    // Update password baru
    $hashed = password_hash($new_password, PASSWORD_BCRYPT);
    $update_query = "UPDATE users SET password = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($koneksi, $update_query);
    mysqli_stmt_bind_param($update_stmt, 'si', $hashed, $user_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        echo json_encode(['success' => true, 'message' => 'Password berhasil diubah']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengubah password']);
    }
    exit();
}

$total_saldo = get_total_saldo($koneksi, $user_id);
$monthly_progress = get_monthly_progress($koneksi, $user_id);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile & Settings - Sistem Tabungan</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <i class="fas fa-piggy-bank"></i>
                <span>SakuKu</span>
            </div>
            <div class="navbar-user">
                <span>Halo, <strong><?php echo escape_output($user['username']); ?></strong>!</span>
                <button id="themeToggle" class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-menu">
                <a href="dashboard.php" class="menu-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="transaksi.php" class="menu-item">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Transaksi</span>
                </a>
                <a href="profile.php" class="menu-item active">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                <a href="logout.php" class="menu-item menu-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <section class="page-header">
                <div>
                    <h1><i class="fas fa-user-circle"></i> Profile & Pengaturan</h1>
                    <p>Kelola akun dan preferensi Anda</p>
                </div>
            </section>

            <!-- Profile Card -->
            <section class="profile-section">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="profile-info">
                            <h2><?php echo escape_output($user['username']); ?></h2>
                            <p>Member sejak <strong><?php echo date('d M Y'); ?></strong></p>
                        </div>
                    </div>

                    <!-- Account Stats -->
                    <div class="account-stats">
                        <div class="stat-box">
                            <div class="stat-value"><?php echo format_rupiah($total_saldo); ?></div>
                            <div class="stat-label">Total Saldo</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value"><?php echo format_rupiah($monthly_progress['deposit']); ?></div>
                            <div class="stat-label">Setor Bulan Ini</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value"><?php echo format_rupiah($monthly_progress['withdraw']); ?></div>
                            <div class="stat-label">Tarik Bulan Ini</div>
                        </div>
                    </div>
                </div>

                <!-- Change Password Form -->
                <div class="settings-card">
                    <h3><i class="fas fa-lock"></i> Ubah Password</h3>
                    
                    <form id="changePasswordForm" class="form-settings">
                        <div class="form-group">
                            <label for="oldPassword">Password Lama</label>
                            <input 
                                type="password" 
                                id="oldPassword" 
                                name="old_password" 
                                placeholder="Masukkan password lama"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="newPassword">Password Baru</label>
                            <input 
                                type="password" 
                                id="newPassword" 
                                name="new_password" 
                                placeholder="Masukkan password baru (min 6 karakter)"
                                minlength="6"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword">Konfirmasi Password Baru</label>
                            <input 
                                type="password" 
                                id="confirmPassword" 
                                name="confirm_password" 
                                placeholder="Ketik ulang password baru"
                                minlength="6"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Password Baru
                        </button>
                    </form>
                </div>

                <!-- Theme Settings -->
                <div class="settings-card">
                    <h3><i class="fas fa-palette"></i> Pengaturan Tampilan</h3>
                    
                    <div class="theme-settings">
                        <div class="theme-option">
                            <label>
                                <input type="radio" name="theme" value="light" checked>
                                <span><i class="fas fa-sun"></i> Mode Terang</span>
                            </label>
                        </div>
                        <div class="theme-option">
                            <label>
                                <input type="radio" name="theme" value="dark">
                                <span><i class="fas fa-moon"></i> Mode Gelap</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Export Data -->
                <div class="settings-card">
                    <h3><i class="fas fa-download"></i> Export Data</h3>
              
                    <div class="export-actions">
                        <button onclick="exportCSV()" class="btn btn-secondary">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </button>
                        <button onclick="exportPDF()" class="btn btn-secondary">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="script.js?v=1.2"></script>
    <script>
        // Change Password Form
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const oldPassword = document.getElementById('oldPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                showAlert('error', 'Password baru tidak cocok');
                return;
            }

            fetch('profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=change_password&old_password=' + encodeURIComponent(oldPassword) + 
                      '&new_password=' + encodeURIComponent(newPassword) + 
                      '&confirm_password=' + encodeURIComponent(confirmPassword)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    document.getElementById('changePasswordForm').reset();
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan');
            });
        });

        // Export CSV
        function exportCSV() {
            window.location.href = 'export.php?format=csv';
        }

        // Export PDF
        function exportPDF() {
            window.location.href = 'export.php?format=pdf';
        }
    </script>
</body>
</html>
