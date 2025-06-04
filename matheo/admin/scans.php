<?php
include 'config.php';
include 'header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$scans = $pdo->prepare("SELECT s.*, f.nom FROM scans s
                        LEFT JOIN fournitures f ON s.code_barre = f.code_barre
                        WHERE s.acces_id = ?
                        ORDER BY s.date_scan ASC");
$scans->execute([$id]);
$data = $scans->fetchAll();
?>

<h1>📦 Produits scannés pour l’accès #<?= $id ?></h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Date</th><th>Code-barres</th><th>Nom</th><th>Type</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $s): ?>
        <tr>
            <td><?= $s['date_scan'] ?></td>
            <td><?= $s['code_barre'] ?></td>
            <td><?= htmlspecialchars($s['nom'] ?? '-') ?></td>
            <td><?= $s['type_action'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
