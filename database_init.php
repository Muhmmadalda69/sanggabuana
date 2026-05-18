<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS sanggabuana CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "SUCCESS\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
