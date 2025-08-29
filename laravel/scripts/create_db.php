<?php

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USERNAME') ?: 'root';
// Empty password is valid and intended per user request
$pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
$db   = getenv('DB_DATABASE') ?: 'drivingtheory';

try {
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "db_ok";
} catch (Throwable $e) {
    echo "db_error: " . $e->getMessage();
    exit(1);
}


