<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Récupérer la position (indice) de l'utilisateur dans le capteur
    $stmt = $pdo->prepare("SELECT empreinte_biometrique FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user) {
        // Appeler le script Python pour chercher la position dans le capteur
        $output = [];
        $returnCode = 0;
        exec("/var/www/html/venv/bin/python3 /var/www/html/supprimer_emp.py '" . $user['empreinte_biometrique'] . "' 2>&1", $output, $returnCode);

        // Supprimer de la base de données
        $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$id]);

        $message = ($returnCode === 0)
            ? "✅ Utilisateur supprimé avec succès."
            : "⚠️ Supprimé de la base mais erreur capteur :<br><pre>" . implode("\n", $output) . "</pre>";
    } else {
        $message = "❌ Utilisateur introuvable.";
    }
} else {
    $message = "❌ Requête invalide.";
}

header("Location: voir_utilisateurs.php?message=" . urlencode($message));
exit;
