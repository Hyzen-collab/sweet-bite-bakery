<?php
/**
 * Sweet Bite Bakery - main settings
 *
 * If your XAMPP setup is different (for example MySQL has a password),
 * change the values in this file only.
 */

// Database connection (XAMPP default: user "root", empty password)
define('DB_DSN', getenv('SWEETBITE_DSN') ?: 'mysql:host=localhost;dbname=sweetbite_db;charset=utf8mb4');
define('DB_USER', getenv('SWEETBITE_DB_USER') ?: 'root');
define('DB_PASS', getenv('SWEETBITE_DB_PASS') ?: '');

// Folder name inside htdocs. Use '' if the site is in the web root.
define('BASE_URL', getenv('SWEETBITE_BASE') !== false ? getenv('SWEETBITE_BASE') : '/sweetbite');

// File locations
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_DIR', ROOT_PATH . '/uploads/');
define('MAX_UPLOAD_BYTES', 2 * 1024 * 1024); // 2 MB

// Sri Lanka Standard Time (UTC+5:30)
date_default_timezone_set('Asia/Colombo');

// Sessions are used for the admin login and the flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}
