<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';
include 'header.php';

$message = "";

// Suppression
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Récupérer l'utilisateur pour trouver la position à supprimer
    $stmt = $pdo->prepare("SELECT empreinte_biometrique FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user) {
        // Exécution du script Python pour supprimer du capteur
        $empreinteHash = bin2hex($user['empreinte_biometrique']);
        $output = [];
        $returnCode = 0;
        exec("/var/www/html/venv/bin/python3 /var/www/html/supprimer_emp.py " . escapeshellarg($empreinteHash) . " 2>&1", $output, $returnCode);

        if ($returnCode === 0) {
            // Suppression de la base
            $del = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $del->execute([$id]);
            $message = "✅ Utilisateur supprimé.";
        } else {
            $message = "⛔ Erreur lors de la suppression :<br><pre>" . implode("\n", $output) . "</pre>";
        }
    } else {
        $message = "⛔ Utilisateur introuvable.";
    }
}

// Récupérer la liste
$utilisateurs = $pdo->query("SELECT id, nom, prenom FROM utilisateurs")->fetchAll();
?>

<h1>📁 Gérer les empreintes</h1>

<?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($utilisateurs as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['nom']) ?></td>
            <td><?= htmlspecialchars($u['prenom']) ?></td>
            <td>
                <a href="?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer aussi l\'empreinte du capteur ?')">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
