<?php
/**
 * ============================================================================
 * API Endpoint: Get Region Data (api/get_region.php)
 * UAS Desain dan Pemrograman Web
 * 
 * Endpoint AJAX untuk mengambil data kewilayahan dari tabel tb_region.
 * Digunakan oleh cascading dropdown di halaman profil.
 * 
 * Parameter GET:
 *   - level (required): Level region (1=Provinsi, 2=Kab/Kota, 3=Kecamatan, 4=Kelurahan)
 *   - parent_code (optional): Kode parent untuk filter data sesuai wilayah terpilih
 * 
 * Response: JSON array berisi {code, name} untuk setiap region yang cocok
 * 
 * Kontribusi:
 *   Budi Riswandy (3420240006) - Seluruh logic API endpoint, query, dan response
 *   Alwin Dwi Kurniawan (3420240019) - Error handling dan validasi input
 * ============================================================================
 */

// Set header JSON
// Kontribusi: Budi Riswandy (3420240006)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Mulai session untuk validasi (opsional)
session_start();

// Include konfigurasi database
require_once __DIR__ . '/../config/database.php';

// Ambil parameter dari GET request
// Kontribusi: Alwin Dwi Kurniawan (3420240019) - Validasi input
$level = isset($_GET['level']) ? (int)$_GET['level'] : 0;
$parentCode = isset($_GET['parent_code']) ? trim($_GET['parent_code']) : '';

// Validasi level harus antara 1-4
if ($level < 1 || $level > 4) {
    echo json_encode(['error' => 'Level tidak valid. Harus antara 1-4.']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Query data region berdasarkan level dan parent_code
    // Kontribusi: Budi Riswandy (3420240006)
    if ($level === 1) {
        // Level 1 (Provinsi): Ambil semua provinsi tanpa filter parent
        $stmt = $pdo->prepare("
            SELECT code, name 
            FROM tb_region 
            WHERE level = ? AND is_active = TRUE
            ORDER BY name ASC
        ");
        $stmt->execute([$level]);
    } else {
        // Level 2-4: Filter berdasarkan parent_code
        // Kab/Kota sesuai provinsi, Kecamatan sesuai kab/kota, Kelurahan sesuai kecamatan
        if (empty($parentCode)) {
            echo json_encode([]);
            exit;
        }
        
        $stmt = $pdo->prepare("
            SELECT code, name 
            FROM tb_region 
            WHERE level = ? AND parent_code = ? AND is_active = TRUE
            ORDER BY name ASC
        ");
        $stmt->execute([$level, $parentCode]);
    }
    
    // Ambil semua hasil dan kirim sebagai JSON
    $regions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($regions);
    
} catch (PDOException $e) {
    // Kirim error response jika query gagal
    // Kontribusi: Alwin Dwi Kurniawan (3420240019)
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mengambil data region.']);
}
