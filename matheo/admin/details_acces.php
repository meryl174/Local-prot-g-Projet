<?php
include 'config.php';
include 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='text-danger'>❌ ID accès invalide.</p>";
    include 'footer.php';
    exit;
}

$idAcces = intval($_GET['id']);

// Récup info accès
$stmt = $pdo->prepare("
    SELECT a.id, u.nom AS utilisateur, a.date_heure, a.duree
    FROM acces a
    JOIN utilisateurs u ON a.utilisateur_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$idAcces]);
$acces = $stmt->fetch();

if (!$acces) {
    echo "<p class='text-danger'>❌ Accès non trouvé.</p>";
    include 'footer.php';
    exit;
}

// ➕ Récup produits scannés regroupés avec quantités
$scans = $pdo->prepare("
    SELECT s.code_barre, f.nom, s.type_action, COUNT(*) AS quantite
    FROM scans s
    JOIN fournitures f ON s.code_barre = f.code_barre
    WHERE s.acces_id = ?
    GROUP BY s.code_barre, s.type_action
    ORDER BY f.nom
");
$scans->execute([$idAcces]);
$produits = $scans->fetchAll();
?>

<h2 class="mb-4">📦 Détails de la session #<?= $acces['id'] ?></h2>
<ul>
    <li><strong>Utilisateur :</strong> <?= htmlspecialchars($acces['utilisateur']) ?></li>
    <li><strong>Date/heure :</strong> <?= $acces['date_heure'] ?></li>
    <?php
    $duration = $acces['duree'];
    $durationFormatted = '-';
    if ($duration !== null) {
        $minutes = floor($duration / 60);
        $seconds = $duration % 60;
        $durationFormatted = sprintf("%02d:%02d", $minutes, $seconds);
    }
    ?>
    <li><strong>Durée :</strong> <?= $durationFormatted ?> minutes</li>
</ul>

<h4 class="mt-4">🧾 Fournitures scannées (avec quantités)</h4>
<?php if (count($produits) === 0): ?>
    <p>Aucun produit scanné pour cette session.</p>
<?php else: ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom produit</th>
                <th>Code-barres</th>
                <th>Action</th>
                <th>Quantité</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($produits as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nom']) ?></td>
                <td><?= $p['code_barre'] ?></td>
                <td><?= $p['type_action'] === 'ENTREE' ? '➕ Entrée' : '➖ Sortie' ?></td>
                <td><?= $p['quantite'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<a href="historique_acces.php" class="btn btn-secondary mt-3">⬅️ Retour</a>

<?php include 'footer.php'; ?>
