<?php
include 'config.php';
include 'header.php';

$message = "";

// Récupère ID d'accès courant
$idAcces = null;
$accesPath = __DIR__ . '/.current_acces';
if (file_exists($accesPath)) {
    $idAcces = intval(trim(file_get_contents($accesPath)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['scan_code']);

    if (strlen($code) < 10) {
        $message = "⛔ Code-barres trop court.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM fournitures WHERE code_barre = ?");
        $stmt->execute([$code]);
        $produit = $stmt->fetch();

        if ($produit) {
            $pdo->prepare("UPDATE fournitures SET quantite_stock = quantite_stock + 1 WHERE code_barre = ?")
                ->execute([$code]);
            $message = "✅ 1 unité ajoutée à : " . htmlspecialchars($produit['nom']);

            if ($idAcces) {
                // Update table acces
                $res = $pdo->prepare("SELECT produits_scannes FROM acces WHERE id = ?");
                $res->execute([$idAcces]);
                $row = $res->fetch(PDO::FETCH_ASSOC);
                $ancien = $row['produits_scannes'] ?? '';
                $nouveau = $ancien ? $ancien . ", ➕ " . $produit['nom'] : "➕ " . $produit['nom'];

                $pdo->prepare("UPDATE acces SET produits_scannes = ? WHERE id = ?")
                    ->execute([$nouveau, $idAcces]);

                // Insert in scans
                $pdo->prepare("INSERT INTO scans (acces_id, code_barre, date_scan, type_action)
                               VALUES (?, ?, NOW(), ?)")
                    ->execute([$idAcces, $code, 'ENTREE']);
            } else {
                $message .= "<br>⚠️ Aucun accès en cours détecté.";
            }
        } else {
            $message = "❌ Produit non trouvé.";
        }
    }
}
?>

<h1 class="mb-4">➕ Entrée de matériel</h1>

<?php if (!empty($message)) : ?>
<div id="messageBox" class="alert alert-info alert-dismissible fade show" role="alert">
    <?= $message ?>
</div>
<?php endif; ?>

<form method="POST" class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Code-barres</label>
        <input type="text" name="scan_code" class="form-control" autofocus autocomplete="off">
    </div>
</form>

<script>
setTimeout(() => {
    const box = document.getElementById("messageBox");
    if (box) {
        box.classList.remove("show");
        box.classList.add("fade");
    }
}, 3000);
</script>

<?php include 'footer.php'; ?>
