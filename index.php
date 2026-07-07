<?php
/**
 * ============================================================================
 * Halaman Login (index.php)
 * UAS Desain dan Pemrograman Web
 * 
 * Halaman utama untuk login pengguna ke aplikasi.
 * Menggunakan session-based authentication dengan password_verify.
 * 
 * Kontribusi:
 *   Alwin Dwi Kurniawan (3420240019) - Struktur HTML login form, logo
 *   Budi Riswandy (3420240006) - Logic autentikasi, validasi, session handling
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

// Proses login saat form disubmit
// Kontribusi: Budi Riswandy (3420240006)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validasi input tidak boleh kosong
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi.';
    } else {
        $pdo = getDBConnection();
        
        // Cari user berdasarkan email menggunakan prepared statement (anti SQL injection)
        $stmt = $pdo->prepare("SELECT id, username, email, password, full_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // Verifikasi password menggunakan password_verify
        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil - set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login - UAS Desain dan Pemrograman Web - Universitas Islam As-Syafi'iyah">
    <title>Login | UAS Web - UIA</title>
    <link rel="icon" href="assets/images/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Halaman Login -->
    <!-- Kontribusi: Alwin Dwi Kurniawan (3420240019) -->
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo dan Judul -->
            <div class="auth-logo">
                <img src="assets/images/logo-uia.png" alt="Logo UIA" id="loginLogo">
                <h1>Selamat Datang</h1>
                <p>Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- Pesan Error -->
            <!-- Kontribusi: Budi Riswandy (3420240006) -->
            <?php if ($error): ?>
                <div class="alert alert-error" id="loginError">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <!-- Kontribusi: Alwin Dwi Kurniawan (3420240019) -->
            <form method="POST" action="index.php" id="loginForm">
                <div class="form-group">
                    <label for="email">Email <span style="color: var(--accent-rose);">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="Masukkan email Anda" 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password <span style="color: var(--accent-rose);">*</span></label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Masukkan password Anda" required style="padding-right: 40px;">
                        <button type="button" class="toggle-password" data-target="password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center;" aria-label="Toggle Password">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="btnLogin">
                    Masuk
                </button>
            </form>

            <!-- Link ke Register -->
            <div class="auth-link">
                Belum punya akun? <a href="register.php" id="linkRegister">Daftar di sini</a>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
