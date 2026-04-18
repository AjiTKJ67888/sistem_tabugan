<?php
// ============================================
// HALAMAN TRANSAKSI DAN HANDLER AJAX
// ============================================

// Mulai session
session_start();

// Hubungkan ke database
require_once 'koneksi.php';
require_once 'functions.php';

// Cek apakah user sudah login
if (!is_logged_in()) {
    if ($_POST['action'] === 'add_transaction') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    } else {
        redirect('login.php');
    }
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle AJAX request untuk menambah transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_transaction') {
    header('Content-Type: application/json');
    
    $type = validate_input($_POST['type'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $description = validate_input($_POST['description'] ?? '');
    
    $result = add_transaction($koneksi, $user_id, $type, $amount, $description);
    
    echo json_encode($result);
    exit();
}

// Ambil data user
$user = get_user_by_id($koneksi, $user_id);
$total_saldo = get_total_saldo($koneksi, $user_id);

// Filter transaksi
$filter = validate_input($_GET['filter'] ?? '');
$all_transactions = get_transactions($koneksi, $user_id, $filter === '' ? null : $filter, 100);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Sistem Tabungan</title>
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
                <a href="transaksi.php" class="menu-item active">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Transaksi</span>
                </a>
                <a href="profile.php" class="menu-item">
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
            <!-- Header -->
            <section class="page-header">
                <div>
                    <h1><i class="fas fa-exchange-alt"></i> Riwayat Transaksi</h1>
                    <p>Kelola dan pantau semua transaksi tabungan Anda</p>
                </div>
            </section>

            <!-- Saldo Info -->
            <section class="saldo-info-card">
                <div class="info-item">
                    <span class="info-label">Saldo Saat Ini</span>
                    <span class="info-value"><?php echo format_rupiah($total_saldo); ?></span>
                </div>
            </section>

            <!-- Filter & Actions -->
            <section class="transactions-filter">
                <div class="filter-buttons">
                    <a href="transaksi.php?filter=" class="filter-btn <?php echo $filter === '' ? 'active' : ''; ?>">
                        <i class="fas fa-th"></i> Semua
                    </a>
                    <a href="transaksi.php?filter=deposit" class="filter-btn <?php echo $filter === 'deposit' ? 'active' : ''; ?>">
                        <i class="fas fa-arrow-down"></i> Pemasukan
                    </a>
                    <a href="transaksi.php?filter=withdraw" class="filter-btn <?php echo $filter === 'withdraw' ? 'active' : ''; ?>">
                        <i class="fas fa-arrow-up"></i> Pengeluaran
                    </a>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-deposit" onclick="showDepositModal()">
                        <i class="fas fa-plus"></i> Setor
                    </button>
                    <button class="btn btn-withdraw" onclick="showWithdrawModal()">
                        <i class="fas fa-minus"></i> Tarik
                    </button>
                </div>
            </section>

            <!-- Transactions Table -->
            <section class="transactions-section">
                <div class="table-responsive">
                    <table class="transactions-table">
                        <thead>
                            <tr>
                                <th>Tanggal & Jam</th>
                                <th>Tipe Transaksi</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $has_transactions = false;
                            if (mysqli_num_rows($all_transactions) > 0) {
                                $has_transactions = true;
                                while ($transaction = mysqli_fetch_assoc($all_transactions)) {
                                    $type_class = $transaction['type'] === 'deposit' ? 'deposit' : 'withdraw';
                                    $type_icon = $transaction['type'] === 'deposit' ? 'arrow-down' : 'arrow-up';
                                    $date = date('d M Y', strtotime($transaction['created_at']));
                                    $time = date('H:i', strtotime($transaction['created_at']));
                                    $description = !empty($transaction['description']) ? $transaction['description'] : '-';
                            ?>
                            <tr class="transaction-row">
                                <td class="date-col">
                                    <div class="date-info">
                                        <span class="date"><?php echo $date; ?></span>
                                        <span class="time"><?php echo $time; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="type-badge type-<?php echo $type_class; ?>">
                                        <i class="fas fa-<?php echo $type_icon; ?>"></i>
                                        <?php echo ucfirst($transaction['type']); ?>
                                    </span>
                                </td>
                                <td class="amount-<?php echo $type_class; ?>">
                                    <strong>
                                        <?php echo $transaction['type'] === 'deposit' ? '+' : '-'; ?><?php echo format_rupiah($transaction['amount']); ?>
                                    </strong>
                                </td>
                                <td><?php echo escape_output($description); ?></td>
                            </tr>
                            <?php 
                                }
                            } 
                            
                            if (!$has_transactions) {
                            ?>
                            <tr>
                                <td colspan="4" class="text-center empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada transaksi</p>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal Deposit -->
    <div id="depositModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-plus"></i> Setor Saldo</h2>
                <button type="button" class="modal-close" onclick="closeDepositModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="depositForm" class="modal-form">
                <div class="form-group">
                    <label for="depositAmount">Nominal (Rp)</label>
                    <input 
                        type="number" 
                        id="depositAmount" 
                        name="amount" 
                        placeholder="Masukkan nominal yang ingin disetor"
                        min="1"
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="depositDesc">Keterangan (Opsional)</label>
                    <input 
                        type="text" 
                        id="depositDesc" 
                        name="description" 
                        placeholder="Cth: Gaji, Bonus, dll"
                    >
                </div>
                <button type="submit" class="btn btn-primary btn-large">
                    <i class="fas fa-check"></i> Setor Sekarang
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Withdraw -->
    <div id="withdrawModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-minus"></i> Tarik Saldo</h2>
                <button type="button" class="modal-close" onclick="closeWithdrawModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="withdrawForm" class="modal-form">
                <div class="form-group">
                    <label for="withdrawAmount">Nominal (Rp)</label>
                    <input 
                        type="number" 
                        id="withdrawAmount" 
                        name="amount" 
                        placeholder="Masukkan nominal yang ingin ditarik"
                        min="1"
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="withdrawDesc">Keterangan (Opsional)</label>
                    <input 
                        type="text" 
                        id="withdrawDesc" 
                        name="description" 
                        placeholder="Cth: Belanja, Bayar Tagihan, dll"
                    >
                </div>
                <button type="submit" class="btn btn-primary btn-large">
                    <i class="fas fa-check"></i> Tarik Sekarang
                </button>
            </form>
        </div>
    </div>

    <script src="script.js?v=1.2"></script>
    <script>
        // Deposit Transaction
        document.getElementById('depositForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const amount = document.getElementById('depositAmount').value;
            const description = document.getElementById('depositDesc').value;

            fetch('transaksi.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'type=deposit&amount=' + amount + '&description=' + encodeURIComponent(description) + '&action=add_transaction'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    closeDepositModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan');
            });
        });

        // Withdraw Transaction
        document.getElementById('withdrawForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const amount = document.getElementById('withdrawAmount').value;
            const description = document.getElementById('withdrawDesc').value;

            fetch('transaksi.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'type=withdraw&amount=' + amount + '&description=' + encodeURIComponent(description) + '&action=add_transaction'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    closeWithdrawModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan');
            });
        });

        // Modal Functions
        function showDepositModal() {
            document.getElementById('depositModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDepositModal() {
            document.getElementById('depositModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('depositForm').reset();
        }

        function showWithdrawModal() {
            document.getElementById('withdrawModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeWithdrawModal() {
            document.getElementById('withdrawModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('withdrawForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('depositModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDepositModal();
            }
        });

        document.getElementById('withdrawModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeWithdrawModal();
            }
        });
    </script>
</body>
</html>
