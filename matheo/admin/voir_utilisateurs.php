<?php
include 'config.php';
include 'header.php';

$utilisateurs = $pdo->query("SELECT * FROM utilisateurs ORDER BY id DESC")->fetchAll();
?>

<h1 class="mb-4">👥 Liste des utilisateurs enregistrés</h1>

<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_GET['message']) ?></div>
<?php endif; ?>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($utilisateurs as $utilisateur): ?>
            <tr>
                <td><?= $utilisateur['id'] ?></td>
                <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
                <td><?= htmlspecialchars($utilisateur['prenom']) ?></td>
                <td>
                    <form method="POST" action="supprimer_utilisateur.php" onsubmit="return confirm('Supprimer cet utilisateur ?');" class="d-inline">
                        <input type="hidden" name="id" value="<?= $utilisateur['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">🗑 Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
