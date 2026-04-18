# 📊 SaveHub - Sistem Tabungan Modern

Aplikasi web sistem tabungan modern yang dibangun dengan **PHP Native (Procedural)**, **HTML5 + CSS3**, **JavaScript (Vanilla)**, dan **MySQL**. Aplikasi ini dirancang khusus untuk berjalan di **Laragon** tanpa konfigurasi tambahan.

---

## 🎯 Fitur Utama

### 1. **Authentication (Autentikasi)**
- ✅ Registrasi akun baru dengan validasi unik username
- ✅ Login dengan keamanan password hash (bcrypt)
- ✅ Logout dan session management
- ✅ Validasi input & konfirmasi password

### 2. **Dashboard Modern**
- ✅ Welcome message dengan nama pengguna
- ✅ Tampilan saldo besar & highlight (gradient biru)
- ✅ Card statistik (Pemasukan, Pengeluaran, Jumlah Transaksi)
- ✅ Grafik transaksi menggunakan Chart.js
- ✅ Riwayat transaksi terbaru

### 3. **Sistem Tabungan**
- ✅ Fitur deposit (setor saldo)
- ✅ Fitur withdraw (tarik saldo)
- ✅ Validasi saldo tidak boleh minus
- ✅ Format nominal dalam rupiah

### 4. **Riwayat Transaksi**
- ✅ Tabel modern dengan stripe & hover effect
- ✅ Filter: Semua, Pemasukan, Pengeluaran
- ✅ Sorting otomatis (terbaru terlebih dahulu)
- ✅ Responsive table untuk mobile

### 5. **UI/UX Modern (Fintech Style)**
- ✅ Tema gradient biru (primary)
- ✅ Aksen hijau (deposit) & merah (withdraw)
- ✅ Card dengan shadow & border radius
- ✅ Navbar minimalis dengan user info
- ✅ Sidebar navigasi yang elegan
- ✅ Modal untuk deposit/withdraw
- ✅ Animasi smooth & hover effect
- ✅ **100% Responsive** (Mobile, Tablet, Desktop)

### 6. **Notifikasi**
- ✅ Alert modern (success & error)
- ✅ Animasi muncul/hilang otomatis
- ✅ Auto-dismiss setelah 5 detik

### 7. **Keamanan Dasar**
- ✅ Prepared Statement MySQLi (SQL Injection prevention)
- ✅ Session management
- ✅ Password hashing dengan password_hash() & password_verify()
- ✅ Input validation & output escaping
- ✅ CSRF protection mindset

---

## 🛠️ Teknologi yang Digunakan

```
Frontend:
- HTML5 (Semantic markup)
- CSS3 (Flexbox & Grid)
- JavaScript Vanilla (Fetch API)
- Font Awesome 6.4 (Icons)
- Google Fonts - Poppins (Typography)
- Chart.js 4.4 (Grafik)

Backend:
- PHP 7.4+ (Procedural)
- MySQL/MySQLi (Database)
- Session Management

Storage:
- LocalStorage (Optional)
- MySQL Transactions
```

---

## 📁 Struktur File

```
website2/
├── database.sql          # SQL untuk membuat database & tabel
├── koneksi.php          # Koneksi ke database
├── functions.php        # Fungsi-fungsi helper
├── register.php         # Halaman registrasi
├── login.php            # Halaman login
├── dashboard.php        # Halaman dashboard
├── transaksi.php        # Halaman riwayat transaksi
├── logout.php           # Handler logout
├── style.css            # Styling aplikasi
├── script.js            # JavaScript interaktif
└── README.md            # File ini
```

---

## ⚡ Quick Start (Laragon)

### **Langkah 1: Persiapan Database**

1. Buka **HeidiSQL** (tools bawaan Laragon)
   - Klik menu "Laragon" → "MySQL" → "HeidiSQL"
   - Atau buka aplikasi HeidiSQL secara langsung

2. Koneksi ke database:
   - Username: `root`
   - Password: (kosong)
   - Database: -
   - Port: `3306`

3. Copy-paste isi file `database.sql`:
   ```sql
   -- Paste semua kode dari database.sql
   ```
   - Klik tombol **Execute** atau tekan **F9**
   - Tunggu sampai selesai (pastikan tidak ada error)

4. Verifikasi:
   - Refresh database list di sebelah kiri
   - Seharusnya muncul database `sistem_tabungan`
   - Buka dan lihat table `users` & `transactions`

### **Langkah 2: Setup File Project**

1. Extract/Copy semua file ke folder:
   ```
   C:\laragon\www\website2\
   ```
   
   Pastikan struktur folder seperti ini:
   ```
   C:\laragon\www\website2\
   ├── database.sql
   ├── koneksi.php
   ├── functions.php
   ├── register.php
   ├── login.php
   ├── dashboard.php
   ├── transaksi.php
   ├── logout.php
   ├── style.css
   ├── script.js
   └── README.md
   ```

### **Langkah 3: Jalankan Aplikasi**

1. Pastikan Laragon sudah running (warna icon hijau)
   - Jika belum, klik icon Laragon → Start All

2. Buka browser dan akses:
   ```
   http://website2.test
   ```

3. Jika tidak bisa, coba:
   ```
   http://localhost/website2
   ```

4. Seharusnya muncul halaman **Register** 
   - Jika halaman kosong/error, cek tools → Console → F12 untuk melihat error

---

## 👤 Testing Account (Contoh Penggunaan)

### **Registrasi Akun Baru**
1. Klik link "Daftar sekarang" di halaman login
2. Isi form:
   - Username: `john_doe` (minimal 3 karakter)
   - Password: `password123` (minimal 6 karakter)
   - Konfirmasi Password: `password123`
3. Klik button "Daftar Sekarang"
4. Seharusnya berhasil & redirect ke login

### **Login**
1. Isi username: `john_doe`
2. Isi password: `password123`
3. Klik "Masuk"
4. Seharusnya masuk ke dashboard

### **Coba Fitur**
Setelah login:
- Klik button "Setor" untuk menambah saldo
- Isi nominal: `50000` dan klik "Setor Sekarang"
- Lihat saldo berubah di card utama
- Klik "Tarik" untuk withdraw saldo
- Lihat riwayat di table transaksi
- Klik menu "Transaksi" untuk melihat history lengkap

---

## 🎨 Fitur UI/UX

### **Warna Palet**
```css
Primary:      #3b82f6 (Biru) - Digunakan untuk buttons, links, highlight
Secondary:   #f3f4f6 (Abu terang) - Background card
Accent Green: #10b981 (Hijau) - Deposit, success
Accent Red:   #ef4444 (Merah) - Withdraw, error
Dark:         #1f2937 (Hitam) - Text
```

### **Responsive Breakpoints**
- 📱 Mobile (< 480px) - Full responsive
- 📱 Tablet (480px - 768px) - Sidebar di bottom
- 💻 Desktop (> 768px) - Sidebar di left
- 🖥️ Large (> 1024px) - Full layout optimal

### **Animasi**
- Slide down untuk alert
- Modal slide in/out
- Button hover dengan transform
- Smooth transitions (0.3s)
- Loading spinner

---

## 📊 Database Schema

### **Table: users**
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### **Table: transactions**
```sql
CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('deposit', 'withdraw') NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## 🔐 Keamanan yang Diimplementasikan

✅ **Password Security**
- Menggunakan `password_hash()` dengan BCRYPT
- Verifikasi dengan `password_verify()`
- Tidak menyimpan plain text password

✅ **Database Security**
- Prepared Statement MySQLi (prevent SQL Injection)
- Parameterized queries
- Input validation sebelum ke database

✅ **Output Security**
- `htmlspecialchars()` untuk escape output
- Prevent XSS attacks
- ENT_QUOTES dan UTF-8 encoding

✅ **Session Security**
- Session management yang proper
- Check login status before accessing page
- Logout menghapus semua session

✅ **Input Validation**
- Server-side validation (PHP)
- Client-side validation (HTML5 + JS)
- Type checking dan sanitization

---

## 🐛 Troubleshooting

### **Error: "Koneksi database gagal"**
```
Solusi:
1. Pastikan MySQL service di Laragon running
2. Cek username 'root' dan password kosong di koneksi.php
3. Cek database 'sistem_tabungan' sudah dibuat
4. Restart Laragon
```

### **Error: "Access Denied for user 'root'@'localhost'"**
```
Solusi:
1. Buka HeidiSQL
2. Masuk dengan user root (kosong password)
3. Klik menu Query → Run SQL
4. Paste code dari database.sql
5. Klik Execute
```

### **Halaman tidak bisa diakses (404)**
```
Solusi:
1. Pastikan file di folder: C:\laragon\www\website2\
2. Akses via: http://website2.test (bukan localhost)
3. Jika tetap 404, cek file .htaccess (mungkin tidak ada)
4. Ke folder Laragon, klik Start → Stop → Start lagi
```

### **Daftar berhasil, tapi tidak bisa login**
```
Solusi:
1. Buka HeidiSQL
2. Check table users apakah sudah ada datanya
3. Pastikan password ter-hash (bukan plain text)
4. Coba daftar ulang dengan username berbeda
```

### **Tidak bisa see data transaksi**
```
Solusi:
1. Refresh halaman (F5 atau Ctrl+R)
2. Check browser console (F12) → Console tab
3. Pastikan sudah login dengan akun yang benar
4. Clear browser cache (Ctrl+Shift+Delete)
```

### **Styling tidak muncul / CSS tidak load**
```
Solusi:
1. Check file style.css ada di folder C:\laragon\www\website2\
2. Hard refresh browser (Ctrl+Shift+R)
3. Check console (F12) apakah ada 404 untuk style.css
4. Pastikan path CSS benar di HTML: href="style.css"
```

---

## 💡 Tips & Tricks

### **Untuk Development**
```javascript
// Buka console browser (F12) untuk debugging
// Lihat semua custom logs dengan prefix [timestamp] SaveHub:

// Test fetch dengan membuka console:
fetch('transaksi.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'type=deposit&amount=10000&action=add_transaction'
})
.then(r => r.json())
.then(d => console.log(d))
.catch(e => console.error(e));
```

### **Untuk Testing**
```bash
# Jalankan query MySQL untuk cek data
SELECT * FROM users;
SELECT * FROM transactions;

# Clear data untuk testing dari awal
DELETE FROM transactions;
DELETE FROM users;
```

### **Best Practices**
1. Selalu validasi input baik di client maupun server
2. Jangan pernah trust input user
3. Gunakan HTTPS untuk production
4. Regular backup database
5. Update PHP dan MySQL secara berkala

---

## 📋 Features Checklist

### **Authentication**
- [x] Register page
- [x] Login page
- [x] Logout functionality
- [x] Session management
- [x] Password hashing (bcrypt)
- [x] Username uniqueness validation
- [x] Password confirmation
- [x] Input validation

### **Dashboard**
- [x] User welcome message
- [x] Total saldo display
- [x] Statistics cards (Income, Expense, Transactions)
- [x] Chart.js integration
- [x] Recent transactions

### **Transactions**
- [x] Deposit feature
- [x] Withdraw feature
- [x] Balance validation
- [x] Modal dialog
- [x] AJAX submission
- [x] Transaction history
- [x] Filter system
- [x] Responsive table

### **UI/UX**
- [x] Modern fintech theme
- [x] Blue gradient primary
- [x] Responsive design
- [x] Navbar
- [x] Sidebar navigation
- [x] Card components
- [x] Button animations
- [x] Alert notifications
- [x] Modal dialogs
- [x] Smooth transitions

### **Technical**
- [x] PHP native (procedural)
- [x] MySQLi prepared statements
- [x] HTML5 semantic markup
- [x] CSS3 Flexbox & Grid
- [x] Vanilla JavaScript
- [x] Fetch API
- [x] Font Awesome CDN
- [x] Google Fonts CDN
- [x] Chart.js CDN
- [x] Code comments
- [x] No errors/warnings

---

## 📞 Support & Documentation

### **File Documentation**
Setiap file sudah dilengkapi dengan:
- Komentar di setiap fungsi
- Penjelasan variabel
- Tips perawatan
- Best practices

### **Struktur Komentar**
```php
<?php
// ============================================
// DESKRIPSI SECTION/FILE
// ============================================

/**
 * Deskripsi fungsi
 * @param {tipe} nama - Penjelasan parameter
 * @returns {tipe} - Penjelasan return
 */
function nama_fungsi($parameter) {
    // Implementasi dengan komentar yang jelas
}
?>
```

---

## 🚀 Production Checklist

Sebelum upload ke production server:

- [ ] Ganti password database (di koneksi.php)
- [ ] Gunakan HTTPS (bukan HTTP)
- [ ] Update keamanan server
- [ ] Setup backup database reguler
- [ ] Optimize database indexes
- [ ] Minify CSS dan JavaScript
- [ ] Setup error logging
- [ ] Configure firewall
- [ ] Setup monitoring
- [ ] Regular security audit

---

## 📜 License

Project ini bebas digunakan untuk keperluan personal maupun komersial.

---

## ✨ Credits

Dibuat dengan ❤️ menggunakan:
- PHP (Procedural)
- MySQL
- HTML5 + CSS3
- JavaScript Vanilla
- Chart.js
- Font Awesome
- Google Fonts

---

## 📝 Catatan Penting

1. **Jangan menghapus** file `koneksi.php` atau `functions.php` karena dibutuhkan oleh file lain
2. **Backup database** secara berkala sebelum testing
3. **Clear browser cache** jika stylesheet tidak update
4. **Gunakan HeidiSQL** untuk manage database (bukan phpMyAdmin jika possible)
5. **Jangan share** database password ke orang lain

---

## 🎓 Learning Resources

Dalam kode ini Anda bisa belajar:
- PHP procedural (bukan OOP)
- MySQLi prepared statements
- HTML5 semantic markup
- CSS3 Grid & Flexbox
- Vanilla JavaScript (no framework)
- Responsive design
- Web security basics
- Database design

---

**Happy Saving! 💰**

Jika ada pertanyaan atau masalah, check troubleshooting section di atas.
