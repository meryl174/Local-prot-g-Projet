<?php
include 'config.php';
include 'header.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empreinte = trim($_POST['empreinte'] ?? '');

    if (strlen($empreinte) < 3) {
        $message = "❌ Empreinte trop courte.";
    } else {
        $fichier = '/var/www/html/Utilisateur/.empreinte.txt';

        // Écriture de l'empreinte
        if (file_put_contents($fichier, $empreinte) !== false) {
            $message = "✅ Empreinte envoyée.";

            // Attendre 1 seconde pour laisser le temps au C++ de lire
            sleep(1);

            // Vider le fichier
            file_put_contents($fichier, '');
        } else {
            $message = "❌ Échec lors de l'envoi.";
        }
    }
}
?>

<div class="container mt-4">
    <h1>🖐️ Simuler une empreinte</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label for="empreinte" class="form-label">Empreinte simulée</label>
            <input type="text" id="empreinte" name="empreinte" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>

<?php include 'footer.php'; ?>
