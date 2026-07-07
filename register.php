<?php
/**
 * ============================================================================
 * Halaman Register (register.php)
 * UAS Desain dan Pemrograman Web
 * 
 * Halaman pendaftaran pengguna baru.
 * Password di-hash menggunakan password_hash() untuk keamanan.
 * Otomatis membuat record user_profiles saat registrasi.
 * 
 * Kontribusi:
 *   Alwin Dwi Kurniawan (3420240019) - Struktur HTML register form
 *   Budi Riswandy (3420240006) - Logic registrasi, hashing, validasi, auto-create profile
 * ============================================================================
 */

session_start();

// Jika sudah login, redirect ke dashboard
// Kontribusi: Budi Riswandy (3420240006)
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Proses registrasi saat form disubmit
// Kontribusi: Budi Riswandy (3420240006)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    
    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validasi input
    if (empty($fullName) || empty($username) || empty($email) || empty($phone) || empty($password)) {
        $error = 'Semua field harus diisi.';
    } elseif (strlen($fullName) < 2 || strlen($fullName) > 50) {
        $error = 'Panjang Nama Lengkap harus antara 2 hingga 50 karakter.';
    } elseif (strlen($username) < 2 || strlen($username) > 50) {
        $error = 'Panjang Username harus antara 2 hingga 50 karakter.';
    } elseif (strlen($phone) < 10 || strlen($phone) > 14) {
        $error = 'Panjang karakter tidak sesuai';
    } elseif (!preg_match('/^[0-9]+$/', $phone)) {
        $error = 'Format Nomor HP tidak valid';
    } elseif (strlen($password) < 8) {
        $error = 'Panjang password min 8 karakter';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
        $error = 'Karakter harus terdiri dari huruf besar, huruf kecil, angka, dan simbol';
    } elseif ($password !== $confirmPassword) {
        $error = 'Konfirmasi password tidak cocok.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $pdo = getDBConnection();
        
        // Cek apakah email atau username sudah terdaftar
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        
        if ($stmt->fetch()) {
            $error = 'Email atau username sudah terdaftar.';
        } else {
            try {
                // Mulai transaksi untuk memastikan konsistensi data
                $pdo->beginTransaction();
                
                // Hash password untuk keamanan
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user baru ke tabel users
                $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, full_name) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $phone, $hashedPassword, $fullName]);
                
                // Ambil ID user yang baru dibuat
                $userId = $pdo->lastInsertId();
                
                // Auto-create record di user_profiles
                $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id) VALUES (?)");
                $stmt->execute([$userId]);
                
                // Commit transaksi
                $pdo->commit();
                
                $success = 'Registrasi berhasil! Silakan login.';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = 'Terjadi kesalahan saat registrasi. Silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Register - UAS Desain dan Pemrograman Web - Universitas Islam As-Syafi'iyah">
    <title>Register | UAS Web - UIA</title>
    <link rel="icon" href="assets/images/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Halaman Register -->
    <!-- Kontribusi: Alwin Dwi Kurniawan (3420240019) -->
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo dan Judul -->
            <div class="auth-logo">
                <img src="assets/images/logo-uia.png" alt="Logo UIA" id="registerLogo">
                <h1>Buat Akun Baru</h1>
                <p>Daftar untuk mulai menggunakan aplikasi</p>
            </div>

            <!-- Pesan Error / Success -->
            <!-- Kontribusi: Budi Riswandy (3420240006) -->
            <?php if ($error): ?>
                <div class="alert alert-error" id="registerError">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" id="registerSuccess">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Form Registrasi -->
            <!-- Kontribusi: Alwin Dwi Kurniawan (3420240019) -->
            <form method="POST" action="register.php" id="registerForm">
                <div class="form-group">
                    <label for="full_name">Nama Lengkap <span style="color: var(--accent-rose);">*</span></label>
                    <input type="text" id="full_name" name="full_name" class="form-control" 
                           placeholder="Masukkan nama lengkap" 
                           value="<?php echo htmlspecialchars($fullName ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="username">Username <span style="color: var(--accent-rose);">*</span></label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Masukkan username" 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email <span style="color: var(--accent-rose);">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="Masukkan email" 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">No HP <span style="color: var(--accent-rose);">*</span></label>
                    <input type="text" id="phone" name="phone" class="form-control" 
                           placeholder="Masukkan nomor HP" 
                           value="<?php echo htmlspecialchars($phone ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password <span style="color: var(--accent-rose);">*</span></label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Minimal 8 karakter" required style="padding-right: 40px;">
                        <button type="button" class="toggle-password" data-target="password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center;" aria-label="Toggle Password">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password <span style="color: var(--accent-rose);">*</span></label>
                    <div style="position: relative;">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                               placeholder="Ulangi password Anda" required style="padding-right: 40px;">
                        <button type="button" class="toggle-password" data-target="confirm_password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center;" aria-label="Toggle Password">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="btnRegister">
                    Daftar
                </button>
            </form>

            <!-- Link ke Login -->
            <div class="auth-link">
                Sudah punya akun? <a href="index.php" id="linkLogin">Masuk di sini</a>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
