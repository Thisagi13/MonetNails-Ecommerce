<?php
// Data Layer: Database connections & global settings

$host = 'localhost';
$db   = 'monet_nails_db';
$user = 'root';
$pass = '220308';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     // Sync MySQL session timezone to PHP's timezone so expires_at comparisons work correctly
     $offset = (new DateTime())->format('P'); // e.g. "+02:00"
     $pdo->exec("SET time_zone = '$offset'");
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
