<?php
/**
 * ============================================================================
 * Header Include - Navbar dengan Logo UIA dan Foto Profil
 * UAS Desain dan Pemrograman Web
 * 
 * Menampilkan:
 * - Logo UIA di pojok kiri atas [15 poin]
 * - Foto profil pengguna di pojok kanan atas [15 poin]
 * - Menu navigasi: Dashboard, Profile, Teams, Logout
 * 
 * Kontribusi:
 *   Alwin Dwi Kurniawan (3420240019) - Struktur navbar, logo, navigasi
 *   Budi Riswandy (3420240006) - Foto profil, responsive menu, active state
 * ============================================================================
 */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
// Kontribusi: Alwin Dwi Kurniawan (3420240019)
if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit;
}

// ============================================================================
// Auto-logout setelah 10 menit tidak aktif
// ============================================================================
$sessionTimeout = 600; // 10 menit dalam detik
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $sessionTimeout) {
        // Session expired, logout otomatis
        session_unset();
        session_destroy();
        header('Location: login?timeout=1');
        exit;
    }
}
$_SESSION['last_activity'] = time();

// Ambil data profil untuk menampilkan foto
// Kontribusi: Budi Riswandy (3420240006)
require_once __DIR__ . '/../config/database.php';
$pdo = getDBConnection();
$stmtProfile = $pdo->prepare("SELECT profile_photo FROM user_profiles WHERE user_id = ?");
$stmtProfile->execute([$_SESSION['user_id']]);
$profileData = $stmtProfile->fetch();
$profilePhoto = ($profileData && $profileData['profile_photo']) ? $profileData['profile_photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['full_name']) . '&background=6366f1&color=fff&size=80';

// Tentukan halaman aktif untuk navbar highlighting
// Kontribusi: Alwin Dwi Kurniawan (3420240019)
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UAS Desain dan Pemrograman Web - Universitas Islam As-Syafi'iyah">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>UAS Web - UIA</title>
    <link rel="icon" href="assets/images/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar - Logo UIA di kiri, Foto Profil di kanan -->
    <!-- Kontribusi: Alwin Dwi Kurniawan (3420240019) -->
    <nav class="navbar" id="mainNavbar">
        <!-- Logo UIA - Pojok Kiri Atas [15 poin] -->
        <a href="dashboard" class="navbar-brand" style="text-decoration: none;">
            <img src="assets/images/logo-uia.png" alt="Logo UIA" id="logoUIA">
            <span>UIA Portal</span>
        </a>

        <!-- Theme Toggle Button -->
        <button id="themeToggleBtn" aria-label="Toggle Dark/Light Mode" style="background: none; border: none; color: var(--text-primary); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 8px; margin-left: auto; margin-right: 15px; border-radius: 50%; transition: background var(--transition-fast);">
            <!-- Sun Icon (Hidden in dark mode by default, handled by JS/CSS) -->
            <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <!-- Moon Icon (Visible in dark mode by default) -->
            <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>

        <!-- Profile Dropdown Menu - Navbar Kanan -->
        <div class="navbar-profile" id="profileDropdownToggle" style="cursor: pointer; position: relative;">
            <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="Profile Photo" id="navProfilePhoto">
            
            <!-- Dropdown Items -->
            <ul class="profile-dropdown-menu" id="profileDropdownMenu">
                <li>
                    <a href="dashboard" class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>" id="navDashboard">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect></svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="profile" class="<?php echo $currentPage === 'profile' ? 'active' : ''; ?>" id="navProfile">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Profile
                    </a>
                </li>
                <li>
                    <a href="teams" class="<?php echo $currentPage === 'teams' ? 'active' : ''; ?>" id="navTeams">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        Teams
                    </a>
                </li>
                <li>
                    <a href="logout" id="navLogout">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Layout Wrapper (Sidebar + Main Content) -->
    <div class="layout-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar fade-in-left">
            <ul class="sidebar-menu">
                <li>
                    <a href="dashboard" class="sidebar-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2z"></path><polyline points="14 2 14 8 22 8"></polyline><polyline points="14 2 14 12"></polyline></svg>
                        <span>My Store</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>Analytics</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <span>Message</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Wrapper -->
        <main class="main-content">
