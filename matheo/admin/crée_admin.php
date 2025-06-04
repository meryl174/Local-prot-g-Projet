<?php
require 'config.php';
$username = 'admin';
$password = 'admin';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
$stmt->execute([$username, $hash]);

echo "Admin créé.";
?>
