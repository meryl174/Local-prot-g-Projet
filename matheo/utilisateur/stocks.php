<?php include 'config.php'; include 'header.php';

// Récupération des fournitures
$stmt = $pdo->query("SELECT * FROM fournitures ORDER BY nom");
$fournitures = $stmt->fetchAll();
?>

<h1 class="mb-4">📊 État du Stock</h1>

<?php if (count($fournitures) === 0) : ?>
    <div class="alert alert-warning">Aucune fourniture enregistrée.</div>
<?php else : ?>
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Nom</th>
                <th>Quantité</th>
                <th>Code-barres</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fournitures as $f) : ?>
                <tr>
                    <td><?= htmlspecialchars($f['nom']) ?></td>
                    <td><?= $f['quantite_stock'] ?></td>
                    <td><?= $f['code_barre'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include 'footer.php'; ?>
