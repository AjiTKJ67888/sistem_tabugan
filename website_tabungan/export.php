<?php
// ============================================
// EXPORT LAPORAN TRANSAKSI
// ============================================

session_start();

require_once 'koneksi.php';
require_once 'functions.php';

// Cek login
if (!is_logged_in()) {
    echo "Belum login";
    exit();
}

$user_id = $_SESSION['user_id'];
$user = get_user_by_id($koneksi, $user_id);

// Ambil semua transaksi
$query = "SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$transactions = mysqli_stmt_get_result($stmt);

// Format untuk export
$format = validate_input($_GET['format'] ?? 'csv');

if ($format === 'csv') {
    // Export ke CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="laporan_tabungan_' . date('Y-m-d') . '.csv"');
    
    // BOM untuk UTF-8
    echo "\xEF\xBB\xBF";
    
    // Header CSV
    $output = "No,Tanggal,Waktu,Tipe,Nominal,Keterangan\n";
    
    $no = 1;
    while ($transaction = mysqli_fetch_assoc($transactions)) {
        $date = date('d-m-Y', strtotime($transaction['created_at']));
        $time = date('H:i', strtotime($transaction['created_at']));
        $type = ucfirst($transaction['type']);
        $amount = $transaction['amount'];
        $description = !empty($transaction['description']) ? $transaction['description'] : '-';
        
        $output .= "$no,\"$date\",\"$time\",\"$type\",\"$amount\",\"$description\"\n";
        $no++;
    }
    
    echo $output;
    exit();

} elseif ($format === 'pdf') {
    // Export ke PDF (HTML format untuk print)
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: inline; filename="laporan_tabungan_' . date('Y-m-d') . '.html"');
    
    $total_saldo = get_total_saldo($koneksi, $user_id);
    $total_deposit = get_total_deposit($koneksi, $user_id);
    $total_withdraw = get_total_withdraw($koneksi, $user_id);
    
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Laporan Tabungan</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: Arial, sans-serif;
                padding: 20px;
                background: white;
                color: #1f2937;
            }
            
            /* Dark mode styles */
            body.dark-mode {
                background: #0f172a;
                color: #f8fafc;
            }
            
            body.dark-mode .header {
                border-bottom-color: #60a5fa;
            }
            
            body.dark-mode .header h1 {
                color: #60a5fa;
            }
            
            body.dark-mode .header p,
            body.dark-mode .info-label,
            body.dark-mode .stat-label,
            body.dark-mode .footer p,
            body.dark-mode .footer {
                color: #cbd5e1;
            }
            
            body.dark-mode .info-box,
            body.dark-mode .stat-card {
                background: #334155;
                border-left-color: #60a5fa;
                border-color: #475569;
            }
            
            body.dark-mode .info-value,
            body.dark-mode .stat-value {
                color: #f8fafc;
            }
            
            body.dark-mode h2 {
                color: #f8fafc;
                border-top-color: #475569;
            }
            
            body.dark-mode table {
                background: #1e293b;
                color: #f8fafc;
            }
            
            body.dark-mode th {
                background: #3b82f6;
                color: #f8fafc;
            }
            
            body.dark-mode td {
                border-bottom-color: #475569;
                color: #f8fafc;
            }
            
            body.dark-mode tr:nth-child(even) {
                background: #334155;
            }
            
            body.dark-mode .deposit {
                color: #10b981;
            }
            
            body.dark-mode .withdraw {
                color: #ef4444;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 2px solid #3b82f6;
            }
            
            .header h1 {
                color: #3b82f6;
                margin-bottom: 10px;
            }
            
            .header p {
                color: #666;
                font-size: 14px;
            }
            
            .user-info {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }
            
            .info-box {
                padding: 15px;
                background: #f3f4f6;
                border-left: 4px solid #3b82f6;
                border-radius: 5px;
            }
            
            .info-label {
                color: #666;
                font-size: 12px;
                margin-bottom: 5px;
            }
            
            .info-value {
                color: #1f2937;
                font-size: 16px;
                font-weight: bold;
            }
            
            .stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }
            
            .stat-card {
                padding: 20px;
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                text-align: center;
            }
            
            .stat-label {
                color: #666;
                font-size: 13px;
                margin-bottom: 8px;
            }
            
            .stat-value {
                color: #1f2937;
                font-size: 20px;
                font-weight: bold;
            }
            
            .stat-value.deposit {
                color: #10b981;
            }
            
            .stat-value.withdraw {
                color: #ef4444;
            }
            
            h2 {
                color: #1f2937;
                margin-bottom: 15px;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 2px solid #e5e7eb;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }
            
            th {
                background: #3b82f6;
                color: white;
                padding: 12px;
                text-align: left;
                font-weight: bold;
            }
            
            td {
                padding: 10px 12px;
                border-bottom: 1px solid #e5e7eb;
            }
            
            tr:nth-child(even) {
                background: #f9fafb;
            }
            
            .deposit {
                color: #10b981;
                font-weight: bold;
            }
            
            .withdraw {
                color: #ef4444;
                font-weight: bold;
            }
            
            .footer {
                text-align: center;
                margin-top: 40px;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
                color: #666;
                font-size: 12px;
            }
            
            @media print {
                body {
                    padding: 0;
                }
                
                .no-print {
                    display: none;
                }
            }
            
            .print-btn {
                display: inline-block;
                margin-bottom: 20px;
                padding: 10px 20px;
                background: #3b82f6;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-family: Arial;
            }
            
            .print-btn:hover {
                background: #2563eb;
            }
            
            body.dark-mode .print-btn {
                background: #3b82f6;
                color: #f8fafc;
            }
            
            body.dark-mode .print-btn:hover {
                background: #2563eb;
            }
        </style>
    </head>
    <body>
        <button class="print-btn no-print" onclick="window.print()">🖨️ Cetak / Print</button>
        
        <div class="header">
            <h1>📊 Laporan Tabungan Anda</h1>
            <p>Aplikasi SakuKu - Sistem Tabungan Modern</p>
        </div>
        
        <div class="user-info">
            <div class="info-box">
                <div class="info-label">Username</div>
                <div class="info-value"><?php echo escape_output($user['username']); ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Tanggal Laporan</div>
                <div class="info-value"><?php echo date('d Maret Y'); ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Waktu Generate</div>
                <div class="info-value"><?php echo date('H:i:s'); ?></div>
            </div>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">Total Saldo</div>
                <div class="stat-value">Rp <?php echo number_format($total_saldo, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Pemasukan</div>
                <div class="stat-value deposit">+ Rp <?php echo number_format($total_deposit, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Pengeluaran</div>
                <div class="stat-value withdraw">- Rp <?php echo number_format($total_withdraw, 0, ',', '.'); ?></div>
            </div>
        </div>
        
        <h2>📋 Riwayat Transaksi Lengkap</h2>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal & Waktu</th>
                    <th>Tipe</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $transactions = mysqli_stmt_get_result($stmt);
                while ($transaction = mysqli_fetch_assoc($transactions)) {
                    $date = date('d-m-Y H:i', strtotime($transaction['created_at']));
                    $type = ucfirst($transaction['type']);
                    $type_class = $transaction['type'] === 'deposit' ? 'deposit' : 'withdraw';
                    $amount = $transaction['amount'];
                    $description = !empty($transaction['description']) ? $transaction['description'] : '-';
                    $symbol = $transaction['type'] === 'deposit' ? '+' : '-';
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $date; ?></td>
                    <td class="<?php echo $type_class; ?>"><?php echo $type; ?></td>
                    <td class="<?php echo $type_class; ?>"><?php echo $symbol; ?> Rp <?php echo number_format($amount, 0, ',', '.'); ?></td>
                    <td><?php echo escape_output($description); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <div class="footer">
            <p>Laporan ini di-generate secara otomatis dari Sistem SakuKu</p>
            <p>Tanggal Generate: <?php echo date('d MMMM Y H:i:s'); ?></p>
        </div>
        
        <script>
            // Apply dark mode if saved in localStorage
            (function() {
                const savedTheme = localStorage.getItem('theme') || 'light';
                if (savedTheme === 'dark') {
                    document.body.classList.add('dark-mode');
                }
            })();
        </script>
    </body>
    </html>
    <?php
    exit();
}
?>
