<?php
/**
 * ============================================================================
 * Database Configuration
 * UAS Desain dan Pemrograman Web
 * 
 * Kontribusi:
 *   Alwin Dwi Kurniawan (3420240019) - Konfigurasi koneksi database PDO
 *   Budi Riswandy (3420240006) - Error handling dan pengaturan charset
 * ============================================================================
 */

// Konfigurasi koneksi database MySQL (Environment-aware)
// Kontribusi: Alwin Dwi Kurniawan (3420240019)
$isLocalhost = false;
if (isset($_SERVER['HTTP_HOST'])) {
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
        $isLocalhost = true;
    }
} elseif (php_sapi_name() === 'cli') {
    // Asumsi CLI berjalan di lokal untuk testing
    $isLocalhost = true;
}

if ($isLocalhost) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'uas_web');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'portalu1_uasweb');
    define('DB_USER', 'portalu1');
    define('DB_PASS', 'egYCf7e945');
}
define('DB_CHARSET', 'utf8mb4');

/**
 * Fungsi untuk mendapatkan koneksi database PDO
 * Menggunakan singleton pattern agar koneksi tidak dibuat berulang
 * 
 * Kontribusi: Budi Riswandy (3420240006)
 * 
 * @return PDO Instance koneksi database
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            // Buat DSN (Data Source Name) untuk koneksi MySQL
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            // Opsi PDO untuk keamanan dan performa
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Tampilkan error sebagai exception
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Fetch sebagai array asosiatif
                PDO::ATTR_EMULATE_PREPARES   => false,                     // Gunakan prepared statement asli
            ];
            
            // Buat koneksi PDO
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            // Tampilkan pesan error jika koneksi gagal
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
    
    return $pdo;
}
