<?php
/**
 * booking.php — Public Booking Entry Point
 * Monet Nails MVC Application
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/BookingController.php';

$controller = new BookingController($pdo);
$controller->index();
