<?php
// ============================================
// HALAMAN DASHBOARD
// ============================================

// Mulai session
session_start();

// Hubungkan ke database
require_once 'koneksi.php';
require_once 'functions.php';

// Cek apakah user sudah login
if (!is_logged_in()) {
    redirect('login.php');
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$user = get_user_by_id($koneksi, $user_id);
$total_saldo = get_total_saldo($koneksi, $user_id);
$total_deposit = get_total_deposit($koneksi, $user_id);
$total_withdraw = get_total_withdraw($koneksi, $user_id);
$total_transactions = get_total_transactions($koneksi, $user_id);

// Ambil transaksi terbaru
$recent_transactions = get_transactions($koneksi, $user_id, null, 5);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Tabungan</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
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
                <a href="dashboard.php" class="menu-item active">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="transaksi.php" class="menu-item">
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
            <!-- Welcome Section -->
            <section class="welcome-section">
                <div class="welcome-text">
                    <h1>Selamat datang kembali, <?php echo escape_output($user['username']); ?>! 👋</h1>
                    <p>Pantau dan kelola tabungan Anda dengan mudah</p>
                </div>
            </section>

            <!-- Saldo Card -->
            <section class="saldo-section">
                <div class="saldo-card">
                    <div class="saldo-header">
                        <span class="saldo-label">Total Saldo</span>
                        <span class="saldo-icon">
                            <i class="fas fa-wallet"></i>
                        </span>
                    </div>
                    <div class="saldo-amount" id="saldoAmount">
                        <?php echo format_rupiah($total_saldo); ?>
                    </div>
                    <div class="saldo-actions">
                        <button class="btn btn-deposit" onclick="showDepositModal()">
                            <i class="fas fa-plus"></i> Setor
                        </button>
                        <button class="btn btn-withdraw" onclick="showWithdrawModal()">
                            <i class="fas fa-minus"></i> Tarik
                        </button>
                    </div>
                </div>
            </section>

            <!-- Statistics Cards -->
            <section class="stats-section">
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-icon deposit">
                            <i class="fas fa-arrow-down"></i>
                        </span>
                        <span class="stat-label">Total Pemasukan</span>
                    </div>
                    <div class="stat-amount">
                        <?php echo format_rupiah($total_deposit); ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-icon withdraw">
                            <i class="fas fa-arrow-up"></i>
                        </span>
                        <span class="stat-label">Total Pengeluaran</span>
                    </div>
                    <div class="stat-amount">
                        <?php echo format_rupiah($total_withdraw); ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-icon transaction">
                            <i class="fas fa-list"></i>
                        </span>
                        <span class="stat-label">Jumlah Transaksi</span>
                    </div>
                    <div class="stat-amount">
                        <?php echo $total_transactions; ?> kali
                    </div>
                </div>
            </section>

            <!-- Chart Section -->
            <section class="chart-section">
                <div class="chart-card">
                    <h3>Grafik Transaksi</h3>
                    <canvas id="transactionChart"></canvas>
                </div>
            </section>

            <!-- Recent Transactions -->
            <section class="recent-transactions">
                <div class="section-header">
                    <h3>Riwayat Transaksi Terbaru</h3>
                    <a href="transaksi.php" class="btn btn-secondary btn-small">Lihat Semua →</a>
                </div>

                <div class="table-responsive">
                    <table class="transactions-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($recent_transactions) > 0) {
                                while ($transaction = mysqli_fetch_assoc($recent_transactions)) {
                                    $type_class = $transaction['type'] === 'deposit' ? 'deposit' : 'withdraw';
                                    $type_icon = $transaction['type'] === 'deposit' ? 'arrow-down' : 'arrow-up';
                                    $date = date('d M Y H:i', strtotime($transaction['created_at']));
                                    $description = !empty($transaction['description']) ? $transaction['description'] : '-';
                            ?>
                            <tr>
                                <td><?php echo $date; ?></td>
                                <td>
                                    <span class="type-badge type-<?php echo $type_class; ?>">
                                        <i class="fas fa-<?php echo $type_icon; ?>"></i>
                                        <?php echo ucfirst($transaction['type']); ?>
                                    </span>
                                </td>
                                <td class="amount-<?php echo $type_class; ?>">
                                    <?php echo $transaction['type'] === 'deposit' ? '+' : '-'; ?><?php echo format_rupiah($transaction['amount']); ?>
                                </td>
                                <td><?php echo escape_output($description); ?></td>
                            </tr>
                            <?php 
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada transaksi</td>
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
        // Data untuk chart
        const depositAmount = <?php echo $total_deposit; ?>;
        const withdrawAmount = <?php echo $total_withdraw; ?>;
        const saldoAmount = <?php echo $total_saldo; ?>;

        // Initialize Chart
        const ctx = document.getElementById('transactionChart').getContext('2d');
        const transactionChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pemasukan', 'Pengeluaran'],
                datasets: [{
                    data: [depositAmount, withdrawAmount],
                    backgroundColor: [
                        '#10b981',
                        '#ef4444',
                    ],
                    borderColor: [
                        '#059669',
                        '#dc2626',
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    padding: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: "'Poppins', sans-serif",
                                size: 14,
                                weight: '500'
                            },
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });

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
