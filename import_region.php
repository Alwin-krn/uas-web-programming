<?php
/**
 * ============================================================================
 * Script Import Region Data
 * UAS Desain dan Pemrograman Web
 * 
 * Script ini melakukan:
 * 1. Membuat database dan tabel jika belum ada
 * 2. Mengimport data region dari file region.sql
 * 3. Mengganti referensi 'master.tb_region' menjadi 'tb_region'
 * 
 * Jalankan script ini SATU KALI saja melalui browser: http://localhost/import_region.php
 * 
 * Kontribusi:
 *   Alwin Dwi Kurniawan (3420240019) - Setup database dan tabel
 *   Budi Riswandy (3420240006) - Logic import dan konversi SQL
 * ============================================================================
 */

// Set timeout lebih lama karena file SQL sangat besar (~91K baris)
set_time_limit(600);
ini_set('memory_limit', '512M');

echo "<h2>Import Region Data - UAS Web</h2>";
echo "<pre>";

try {
    // ========================================================================
    // Step 1: Buat koneksi ke MySQL tanpa database dulu
    // Kontribusi: Alwin Dwi Kurniawan (3420240019)
    // ========================================================================
    echo "1. Menghubungkan ke MySQL...\n";
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "   ✅ Koneksi berhasil\n\n";

    // ========================================================================
    // Step 2: Buat database
    // Kontribusi: Alwin Dwi Kurniawan (3420240019)
    // ========================================================================
    echo "2. Membuat database 'uas_web'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS uas_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE uas_web");
    echo "   ✅ Database siap\n\n";

    // ========================================================================
    // Step 3: Buat tabel-tabel
    // Kontribusi: Alwin Dwi Kurniawan (3420240019)
    // ========================================================================
    echo "3. Membuat tabel...\n";
    
    // Tabel users
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "   ✅ Tabel 'users' dibuat\n";
    
    // Tabel user_profiles
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_profiles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL UNIQUE,
        address TEXT DEFAULT NULL,
        province_code VARCHAR(10) DEFAULT NULL,
        city_code VARCHAR(10) DEFAULT NULL,
        district_code VARCHAR(10) DEFAULT NULL,
        village_code VARCHAR(10) DEFAULT NULL,
        profile_photo VARCHAR(255) DEFAULT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "   ✅ Tabel 'user_profiles' dibuat\n";
    
    // Tabel tb_region
    $pdo->exec("CREATE TABLE IF NOT EXISTS tb_region (
        code VARCHAR(10) NOT NULL PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        level INT NOT NULL,
        parent_code VARCHAR(10) DEFAULT '',
        parent_name VARCHAR(255) DEFAULT '',
        is_active BOOLEAN DEFAULT TRUE,
        created_by VARCHAR(50) DEFAULT 'SYSTEM',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_by VARCHAR(50) DEFAULT 'SYSTEM',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_level (level),
        INDEX idx_parent_code (parent_code),
        INDEX idx_level_parent (level, parent_code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "   ✅ Tabel 'tb_region' dibuat\n\n";

    // ========================================================================
    // Step 4: Cek apakah data region sudah ada
    // Kontribusi: Budi Riswandy (3420240006)
    // ========================================================================
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tb_region");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    if ($count > 0) {
        echo "4. Data region sudah ada ($count records). Melewati import.\n";
        echo "   ℹ️ Jika ingin import ulang, kosongkan tabel tb_region terlebih dahulu.\n\n";
    } else {
        // ====================================================================
        // Step 4: Import data dari region.sql
        // Kontribusi: Budi Riswandy (3420240006)
        // ====================================================================
        echo "4. Mengimport data region dari region.sql...\n";
        echo "   ⏳ File sangat besar (~91K baris), mohon tunggu...\n";
        
        $sqlFile = __DIR__ . '/region.sql';
        
        if (!file_exists($sqlFile)) {
            throw new Exception("File region.sql tidak ditemukan!");
        }
        
        // Baca file SQL
        $sql = file_get_contents($sqlFile);
        
        // Ganti 'master.tb_region' menjadi 'tb_region'
        // Kontribusi: Budi Riswandy (3420240006)
        $sql = str_replace('master.tb_region', 'tb_region', $sql);
        
        // Eksekusi SQL
        $pdo->exec($sql);
        
        // Verifikasi jumlah data yang diimport
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM tb_region");
        $imported = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "   ✅ Berhasil import $imported data region\n\n";
        
        // Tampilkan statistik per level
        echo "   Statistik:\n";
        $levels = [1 => 'Provinsi', 2 => 'Kab/Kota', 3 => 'Kecamatan', 4 => 'Kelurahan/Desa'];
        foreach ($levels as $lvl => $label) {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM tb_region WHERE level = $lvl");
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            echo "   - Level $lvl ($label): $total\n";
        }
    }
    
    echo "\n✅ SETUP SELESAI! Silakan buka index.php untuk login.\n";
    echo "\n📌 Langkah selanjutnya:\n";
    echo "   1. Buka http://localhost/register.php untuk membuat akun baru\n";
    echo "   2. Login di http://localhost/index.php\n";
    echo "   3. Lengkapi profil di halaman Profile\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
