<?php
// ============================================
// FILE FUNGSI-FUNGSI PEMBANTU
// ============================================

// Validasi input string
function validate_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Escape output untuk keamanan
function escape_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Cek apakah user sudah login
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Redirect ke halaman lain
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

// Format rupiah dari angka
function format_rupiah($nominal) {
    return 'Rp ' . number_format($nominal, 0, ',', '.');
}

// Hitung total saldo pengguna
function get_total_saldo($koneksi, $user_id) {
    $query = "SELECT 
        COALESCE(SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END), 0) as total_deposit,
        COALESCE(SUM(CASE WHEN type = 'withdraw' THEN amount ELSE 0 END), 0) as total_withdraw
        FROM transactions 
        WHERE user_id = ?";
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    $total_saldo = ($row['total_deposit'] ?? 0) - ($row['total_withdraw'] ?? 0);
    return $total_saldo;
}

// Hitung total pemasukan
function get_total_deposit($koneksi, $user_id) {
    $query = "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'deposit'";
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return $row['total'] ?? 0;
}

// Hitung total pengeluaran
function get_total_withdraw($koneksi, $user_id) {
    $query = "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'withdraw'";
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return $row['total'] ?? 0;
}

// Hitung jumlah transaksi
function get_total_transactions($koneksi, $user_id) {
    $query = "SELECT COUNT(*) as total FROM transactions WHERE user_id = ?";
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return $row['total'] ?? 0;
}

// Ambil riwayat transaksi
function get_transactions($koneksi, $user_id, $type = null, $limit = 10) {
    if ($type && in_array($type, ['deposit', 'withdraw'])) {
        $query = "SELECT * FROM transactions WHERE user_id = ? AND type = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'isi', $user_id, $type, $limit);
    } else {
        $query = "SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $limit);
    }
    
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// Tambah transaksi baru
function add_transaction($koneksi, $user_id, $type, $amount, $description = '') {
    // Validasi tipe transaksi
    if (!in_array($type, ['deposit', 'withdraw'])) {
        return ['success' => false, 'message' => 'Tipe transaksi tidak valid'];
    }
    
    // Validasi amount
    if ($amount <= 0) {
        return ['success' => false, 'message' => 'Nominal harus lebih dari 0'];
    }
    
    // Untuk withdraw, cek saldo
    if ($type === 'withdraw') {
        $saldo = get_total_saldo($koneksi, $user_id);
        if ($saldo < $amount) {
            return ['success' => false, 'message' => 'Saldo tidak cukup'];
        }
    }
    
    // Insert transaksi
    $query = "INSERT INTO transactions (user_id, type, amount, description, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($description === '') {
        $description = null;
    }
    
    mysqli_stmt_bind_param($stmt, 'isds', $user_id, $type, $amount, $description);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true, 'message' => 'Transaksi berhasil'];
    } else {
        return ['success' => false, 'message' => 'Transaksi gagal'];
    }
}

// Ambil data user berdasarkan ID
function get_user_by_id($koneksi, $user_id) {
    $query = "SELECT id, username FROM users WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt)->fetch_assoc();
}

// Hitung statistik per kategori transaksi
function get_stats_by_category($koneksi, $user_id) {
    $query = "SELECT 
        type as category,
        COUNT(*) as jumlah,
        SUM(amount) as total
        FROM transactions 
        WHERE user_id = ? 
        GROUP BY type";
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// Hitung statistik per bulan
function get_stats_monthly($koneksi, $user_id, $month_offset = 0) {
    $target_month = date('Y-m', strtotime("$month_offset months"));
    
    $query = "SELECT 
        DATE(created_at) as tanggal,
        type,
        SUM(amount) as total,
        COUNT(*) as jumlah
        FROM transactions 
        WHERE user_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?
        GROUP BY DATE(created_at), type
        ORDER BY created_at DESC";
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'is', $user_id, $target_month);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// Get target tabungan (jika ada tabel goals)
function get_user_goals($koneksi, $user_id) {
    // Ambil dari session atau setting
    $goals = [
        'monthly_target' => 1000000, // Default 1 juta
        'annual_target' => 10000000  // Default 10 juta
    ];
    return $goals;
}

// Hitung progres tabungan bulanan
function get_monthly_progress($koneksi, $user_id) {
    $current_month = date('Y-m');
    
    $query = "SELECT 
        COALESCE(SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END), 0) as deposit,
        COALESCE(SUM(CASE WHEN type = 'withdraw' THEN amount ELSE 0 END), 0) as withdraw
        FROM transactions 
        WHERE user_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?";
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'is', $user_id, $current_month);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $result->fetch_assoc();
}

// Ambil data user berdasarkan username
function get_user_by_username($koneksi, $username) {
    $query = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt)->fetch_assoc();
}

// Daftar user baru
function register_user($koneksi, $username, $password, $confirm_password) {
    // Validasi inputan
    if (empty($username) || empty($password) || empty($confirm_password)) {
        return ['success' => false, 'message' => 'Semua field harus diisi'];
    }
    
    // Validasi panjang username
    if (strlen($username) < 3) {
        return ['success' => false, 'message' => 'Username minimal 3 karakter'];
    }
    
    // Validasi panjang password
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password minimal 6 karakter'];
    }
    
    // Cek konfirmasi password
    if ($password !== $confirm_password) {
        return ['success' => false, 'message' => 'Password tidak cocok'];
    }
    
    // Cek username sudah ada
    $existing_user = get_user_by_username($koneksi, $username);
    if ($existing_user) {
        return ['success' => false, 'message' => 'Username sudah terdaftar'];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user baru
    $query = "INSERT INTO users (username, password, created_at) VALUES (?, ?, NOW())";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $hashed_password);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true, 'message' => 'Registrasi berhasil'];
    } else {
        return ['success' => false, 'message' => 'Registrasi gagal'];
    }
}

// Login user
function login_user($koneksi, $username, $password) {
    // Validasi inputan
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username dan password harus diisi'];
    }
    
    // Ambil data user
    $user = get_user_by_username($koneksi, $username);
    
    if (!$user) {
        return ['success' => false, 'message' => 'Username atau password salah'];
    }
    
    // Verifikasi password
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Username atau password salah'];
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    
    return ['success' => true, 'message' => 'Login berhasil'];
}

?>
