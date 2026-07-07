<?php
require_once __DIR__ . '/config/database.php';
$pdo = getDBConnection();

try {
    // Cek kolom gender
    $stmt = $pdo->query("SHOW COLUMNS FROM user_profiles LIKE 'gender'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE user_profiles ADD COLUMN gender ENUM('L', 'P') NULL AFTER address");
        echo "Kolom gender berhasil ditambahkan.\n";
    }

    // Cek kolom birth_date
    $stmt = $pdo->query("SHOW COLUMNS FROM user_profiles LIKE 'birth_date'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE user_profiles ADD COLUMN birth_date DATE NULL AFTER gender");
        echo "Kolom birth_date berhasil ditambahkan.\n";
    }

    echo "Selesai!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
