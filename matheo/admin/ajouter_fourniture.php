<?php include 'config.php'; include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $quantite = $_POST['quantite'];
    $code = $_POST['code_barre'];

    $stmt = $pdo->prepare("INSERT INTO fournitures (nom, quantite_stock, code_barre) VALUES (?, ?, ?)");
    $stmt->execute([$nom, $quantite, $code]);

    header("Location: index.php");
    exit;
}
?>

<h1>Ajouter une fourniture</h1>
<form method="POST">
    <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="nom" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Quantité</label>
        <input type="number" name="quantite" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Code-barres</label>
        <input type="text" name="code_barre" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Ajouter</button>
    <a href="index.php" class="btn btn-secondary">Retour</a>
</form>

<?php include 'footer.php'; ?>
