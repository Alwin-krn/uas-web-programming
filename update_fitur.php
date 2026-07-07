<?php
require_once 'config/database.php';
$pdo = getDBConnection();

try {
    $pdo->beginTransaction();

    // 1. Tambah kolom di user_profiles
    $pdo->exec("ALTER TABLE user_profiles 
                ADD COLUMN first_name VARCHAR(50) DEFAULT NULL,
                ADD COLUMN last_name VARCHAR(50) DEFAULT NULL,
                ADD COLUMN is_active BOOLEAN DEFAULT TRUE");

    // 2. Buat tabel recent_orders
    $pdo->exec("CREATE TABLE IF NOT EXISTS recent_orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_name VARCHAR(100) NOT NULL,
        order_date DATE NOT NULL,
        status ENUM('Completed', 'Pending', 'Process', 'Canceled') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Buat tabel todos
    $pdo->exec("CREATE TABLE IF NOT EXISTS todos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        task_name VARCHAR(255) NOT NULL,
        is_completed BOOLEAN DEFAULT FALSE,
        color_accent VARCHAR(20) DEFAULT 'primary',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 4. Seed Dummy Data untuk recent_orders
    $pdo->exec("INSERT INTO recent_orders (user_name, order_date, status) VALUES 
        ('Michael John', '2023-10-18', 'Completed'),
        ('Ryan Doe', '2023-08-01', 'Pending'),
        ('Terry White', '2023-10-14', 'Process'),
        ('Selma', '2023-02-01', 'Pending'),
        ('Andreas Doe', '2023-10-31', 'Completed')
    ");

    // 5. Seed Dummy Data untuk todos
    $stmtUser = $pdo->query("SELECT id FROM users LIMIT 1");
    $user = $stmtUser->fetch();
    if ($user) {
        $userId = $user['id'];
        $pdo->exec("INSERT INTO todos (user_id, task_name, color_accent) VALUES 
            ($userId, 'Check Inventory', 'blue'),
            ($userId, 'Manage Delivery Team', 'blue'),
            ($userId, 'Contact Selma: Confirm Delivery', 'orange'),
            ($userId, 'Update Shop Catalogue', 'orange'),
            ($userId, 'Count Profit Analytics', 'orange')
        ");
    }

    $pdo->commit();
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
    echo "<h1 style='color:green;'>✅ Update Database Berhasil!</h1>";
    echo "<p>Tabel Recent Orders, Todos, dan kolom Profile baru sudah dibuat dan diisi data contoh.</p>";
    echo "<a href='dashboard.php' style='display:inline-block; padding:10px 20px; background:#6366f1; color:white; text-decoration:none; border-radius:5px;'>Kembali ke Dashboard</a>";
    echo "</div>";
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
    if (strpos($e->getMessage(), 'Duplicate column name') !== false || strpos($e->getMessage(), 'already exists') !== false) {
        echo "<h1 style='color:green;'>✅ Update Sudah Pernah Dilakukan!</h1>";
        echo "<p>Semua kolom sudah siap.</p>";
    } else {
        echo "<h1 style='color:red;'>❌ Terjadi Kesalahan!</h1>";
        echo "<pre style='background:#f4f4f4; padding:20px; display:inline-block; text-align:left;'>" . $e->getMessage() . "</pre>";
    }
    echo "<br><a href='dashboard.php' style='display:inline-block; margin-top:20px; padding:10px 20px; background:#6366f1; color:white; text-decoration:none; border-radius:5px;'>Kembali ke Dashboard</a>";
    echo "</div>";
}
?>
