<?php
/**
 * ============================================================================
 * Halaman Dashboard (dashboard.php)
 * UAS Desain dan Pemrograman Web
 * 
 * Menampilkan dashboard utama aplikasi dengan:
 * - Logo UIA di pojok kiri atas (via header.php) [15 poin]
 * - Foto profil di pojok kanan atas (via header.php) [15 poin]
 * - Welcome message dan quick action cards
 * 
 * Kontribusi:
 *   Alwin Dwi Kurniawan (3420240019) - Layout dashboard, stat cards, welcome section
 *   Budi Riswandy (3420240006) - Quick action cards, data query, info section
 * ============================================================================
 */

$pageTitle = 'Dashboard';
require_once 'includes/header.php';

// Ambil data statistik untuk dashboard
// Kontribusi: Budi Riswandy (3420240006)
$pdo = getDBConnection();

// Hitung total user terdaftar
$stmtUsers = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmtUsers->fetch()['total'];

// Hitung total data region
$stmtRegions = $pdo->query("SELECT COUNT(*) as total FROM tb_region");
$totalRegions = $stmtRegions->fetch()['total'];

// Hitung total provinsi
$stmtProvinces = $pdo->query("SELECT COUNT(*) as total FROM tb_region WHERE level = 1");
$totalProvinces = $stmtProvinces->fetch()['total'];

// Cek apakah profil sudah lengkap
$stmtMyProfile = $pdo->prepare("SELECT province_code, address FROM user_profiles WHERE user_id = ?");
$stmtMyProfile->execute([$_SESSION['user_id']]);
$myProfile = $stmtMyProfile->fetch();
$profileComplete = ($myProfile && $myProfile['province_code']) ? 'Lengkap' : 'Belum Lengkap';
?>

    <div class="container">
        <!-- Welcome Section -->
        <!-- Kontribusi: Alwin Dwi Kurniawan (3420240019) -->
        <div class="dashboard-welcome fade-in-up">
            <h1>Halo, <span class="welcome-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span></h1>
            <p>Selamat datang di portal UAS Desain dan Pemrograman Web — Universitas Islam As-Syafi'iyah</p>
        </div>

        <!-- Stat Cards -->
        <!-- Kontribusi: Alwin Dwi Kurniawan (3420240019) -->
        <div class="dashboard-grid">
            <div class="stat-card fade-in-up stagger-1" id="statUsers">
                <div class="stat-icon purple">👥</div>
                <div class="stat-info">
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Total Pengguna</p>
                </div>
            </div>

            <div class="stat-card fade-in-up stagger-2" id="statRegions">
                <div class="stat-icon cyan">🗺️</div>
                <div class="stat-info">
                    <h3><?php echo number_format($totalRegions); ?></h3>
                    <p>Data Wilayah</p>
                </div>
            </div>

            <div class="stat-card fade-in-up stagger-3" id="statProvinces">
                <div class="stat-icon emerald">🏛️</div>
                <div class="stat-info">
                    <h3><?php echo $totalProvinces; ?></h3>
                    <p>Provinsi</p>
                </div>
            </div>

            <div class="stat-card fade-in-up stagger-4" id="statProfile">
                <div class="stat-icon amber">📋</div>
                <div class="stat-info">
                    <h3><?php echo $profileComplete; ?></h3>
                    <p>Status Profil</p>
                </div>
            </div>
        </div>

        <!-- Project Info Card -->
        <!-- Kontribusi: Budi Riswandy (3420240006) -->
        <div class="glass-card fade-in-up stagger-1" style="margin-top: 2rem; border-left: 4px solid var(--accent-primary); position: relative; overflow: hidden;">
            <!-- Decorative background element -->
            <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: var(--gradient-primary); filter: blur(50px); opacity: 0.2; border-radius: 50%;"></div>
            
            <div style="display: flex; gap: 1.5rem; align-items: flex-start; position: relative; z-index: 1;">
                <div style="font-size: 2.8rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));">🚀</div>
                <div>
                    <h2 style="margin-bottom: 0.5rem; font-size: 1.5rem; font-family: var(--font-heading);">Sistem Informasi Wilayah Terpadu</h2>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem; line-height: 1.7;">
                        Aplikasi ini ibarat buku alamat super pintar! Anda bisa memilih <strong>Provinsi</strong>, lalu daftarnya otomatis mengerucut ke <strong>Kabupaten/Kota</strong>, berlanjut ke <strong>Kecamatan</strong>, hingga <strong>Kelurahan/Desa</strong>. Semua proses pencarian dari <strong>91.000+ daerah</strong> di seluruh Indonesia ini berjalan sangat cepat dan instan.
                    </p>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <a href="profile.php" class="btn btn-primary" style="border-radius: var(--radius-full); padding: 10px 24px;">
                            <span>Melengkapi Profil</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 5px;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                        <a href="teams.php" class="btn btn-outline" style="border-radius: var(--radius-full); padding: 10px 24px;">Lihat Tim Pengembang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
