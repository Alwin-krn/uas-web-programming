<?php
// router.php - Untuk menjalankan PHP Built-in Server agar mendukung .htaccess URL Rewrite
// Cara pakai: php -S localhost:8000 router.php

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Jika file static (.css, .js, .png, dll) ada, biarkan server built-in melayaninya
if (file_exists(__DIR__ . $path) && is_file(__DIR__ . $path)) {
    return false;
}

// Emulasi rewrite rule dari .htaccess
// Jika path adalah / atau /login, arahkan ke index.php
if ($path === '/' || $path === '/login' || $path === '/index') {
    require 'index.php';
    return true;
}

// Jika path cocok dengan file .php (contoh: /register diarahkan ke register.php)
$phpFile = __DIR__ . $path . '.php';
if (file_exists($phpFile) && is_file($phpFile)) {
    // Set $_SERVER['SCRIPT_NAME'] agar framework/script merasa berjalan normal
    $_SERVER['SCRIPT_NAME'] = $path . '.php';
    require $phpFile;
    return true;
}

// Fallback 404
http_response_code(404);
echo "404 Not Found";
?>
