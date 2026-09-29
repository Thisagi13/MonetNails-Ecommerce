<?php
/**
 * logout.php shim
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/AuthController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new AuthController($pdo);
$auth->logout();
