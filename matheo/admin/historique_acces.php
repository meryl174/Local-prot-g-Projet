<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';
include 'header.php';

$stmt = $pdo->query("
    SELECT a.id, u.nom AS utilisateur, a.date_heure, a.duree, a.video_enregistree
    FROM acces a
    JOIN utilisateurs u ON a.utilisateur_id = u.id
    ORDER BY a.date_heure DESC
");
$acces = $stmt->fetchAll();
?>

<h1 class="mb-4">📜 Historique des accès</h1>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Nom utilisateur</th>
            <th>Date/Heure entrée</th>
            <th>Durée (min)</th>
            <th>Vidéo</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($acces as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['utilisateur']) ?></td>
            <td><?= $a['date_heure'] ?></td>
            <?php
$dur = $a['duree'];
$format = '-';
if ($dur !== null) {
    $min = floor($dur / 60);
    $sec = $dur % 60;
    $format = sprintf("%02d:%02d", $min, $sec);
}
?>
<td><?= $format ?></td>

            <td><?= $a['video_enregistree'] ?? '-' ?></td>
            <td>
                <a href="details_acces.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-primary">🔍 Voir</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
