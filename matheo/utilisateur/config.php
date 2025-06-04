<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$host = '10.10.0.20:3306';
$dbname = 'gestion_stockage';
$user = 'adminweb';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
