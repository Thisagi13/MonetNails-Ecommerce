<?php
/**
 * admin/dashboard.php — Admin Dashboard entry point
 * Monet Nails MVC
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AdminController.php';

$controller = new AdminController($pdo);
$controller->dashboard();
