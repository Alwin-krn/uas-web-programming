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

// Ambil data Recent Orders (dummy)
$stmtOrders = $pdo->query("SELECT * FROM recent_orders ORDER BY id DESC LIMIT 5");
$recentOrders = $stmtOrders ? $stmtOrders->fetchAll() : [];

// Ambil data Todos (dummy)
$stmtTodos = $pdo->query("SELECT * FROM todos ORDER BY id ASC LIMIT 5");
$todos = $stmtTodos ? $stmtTodos->fetchAll() : [];
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

        <!-- Dashboard Panels (Recent Orders & Todos) -->
        <div class="dashboard-panels fade-in-up stagger-2">
            <!-- Recent Orders -->
            <div class="glass-card">
                <div class="glass-card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: none; margin-bottom: 0;">
                    <h2 style="font-size: 1.25rem;">Recent Orders</h2>
                    <div style="color: var(--text-secondary); cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Date Order</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recentOrders) > 0): ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--border-color); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: var(--text-secondary);">
                                                    <?php echo substr($order['user_name'], 0, 2); ?>
                                                </div>
                                                <?php echo htmlspecialchars($order['user_name']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo date('d-m-Y', strtotime($order['order_date'])); ?></td>
                                        <td>
                                            <?php 
                                            $badgeClass = 'badge-process';
                                            if ($order['status'] === 'Completed') $badgeClass = 'badge-completed';
                                            if ($order['status'] === 'Pending') $badgeClass = 'badge-pending';
                                            if ($order['status'] === 'Canceled') $badgeClass = 'badge-canceled';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; color: var(--text-secondary);">Belum ada data pesanan (Jalankan update_fitur.php)</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Todos -->
            <div class="glass-card">
                <div class="glass-card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: none; margin-bottom: 0;">
                    <h2 style="font-size: 1.25rem;">Todos</h2>
                    <div style="color: var(--text-secondary); cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </div>
                </div>
                <div class="todo-list">
                    <?php if (count($todos) > 0): ?>
                        <?php foreach ($todos as $todo): ?>
                            <div class="todo-item accent-<?php echo htmlspecialchars($todo['color_accent'] ?? 'blue'); ?>">
                                <span class="todo-text"><?php echo htmlspecialchars($todo['task_name']); ?></span>
                                <div style="color: var(--text-secondary); cursor: pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="todo-item" style="border-left-color: var(--border-color);">
                            <span class="todo-text" style="color: var(--text-secondary);">Belum ada todo (Jalankan update_fitur.php)</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
