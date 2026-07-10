<?php
/**
 * ============================================================================
 * Export Orders to CSV
 * ============================================================================
 */
session_start();
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access Denied');
}

require_once 'config/database.php';
$pdo = getDBConnection();

// Ambil data recent orders
$stmt = $pdo->query("SELECT user_name, order_date, status FROM recent_orders ORDER BY id DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set header HTTP untuk download file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="recent_orders_' . date('Ymd_His') . '.csv"');

// Buat pointer ke memory
$output = fopen('php://output', 'w');

// Header kolom CSV
fputcsv($output, ['User Name', 'Order Date', 'Status']);

// Isi baris data
foreach ($orders as $row) {
    // Pencegahan CSV Injection (Formula Injection di Excel)
    $username = $row['user_name'];
    if (preg_match('/^[=\+\-@]/', $username)) {
        $username = "'" . $username;
    }

    fputcsv($output, [
        $username,
        $row['order_date'],
        $row['status']
    ]);
}

fclose($output);
exit;
?>
