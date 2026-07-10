<?php
/**
 * ============================================================================
 * Logout Handler (logout.php)
 * UAS Desain dan Pemrograman Web
 * 
 * Menghancurkan session dan redirect ke halaman login.
 * 
 * Kontribusi:
 *   Alwin Dwi Kurniawan (3420240019) - Session destroy logic
 *   Budi Riswandy (3420240006) - Redirect handling
 * ============================================================================
 */

session_start();

// Hapus semua data session
// Kontribusi: Alwin Dwi Kurniawan (3420240019)
$_SESSION = [];

// Hapus cookie session jika ada
// Kontribusi: Budi Riswandy (3420240006)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header('Location: login');
exit;
