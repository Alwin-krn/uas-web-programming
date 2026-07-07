-- ============================================================================
-- UAS Desain dan Pemrograman Web
-- Setup Database Script
-- 
-- Kontribusi:
--   Alwin Dwi Kurniawan (3420240019) - Merancang struktur database
--   Budi Riswandy (3420240006) - Merancang tabel region dan relasi
-- ============================================================================

-- Buat database
CREATE DATABASE IF NOT EXISTS uas_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE uas_web;

-- ============================================================================
-- Tabel Users - Menyimpan data login pengguna
-- Kontribusi: Alwin Dwi Kurniawan (3420240019)
-- ============================================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Tabel User Profiles - Menyimpan profil lengkap pengguna termasuk data kewilayahan
-- Kontribusi: Budi Riswandy (3420240006)
-- ============================================================================
CREATE TABLE IF NOT EXISTS user_profiles (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Tabel Region (tb_region) - Menyimpan data kewilayahan Indonesia
-- Level: 1=Provinsi, 2=Kab/Kota, 3=Kecamatan, 4=Kelurahan/Desa
-- Kontribusi: Budi Riswandy (3420240006)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tb_region (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
