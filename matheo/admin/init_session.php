<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_acces'])) {
    $_SESSION['id_acces'] = intval($_POST['id_acces']);
    echo "✅ ID accès enregistré dans la session.";
} else {
    echo "❌ Requête invalide ou ID manquant.";
}
?>
