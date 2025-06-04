<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';
include 'header.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);

    if (!empty($nom) && !empty($prenom)) {
        $output = [];
        $returnCode = 0;

        exec('sudo /var/www/html/venv/bin/python3 /var/www/html/enregistrer_emp.py 2>&1', $output, $returnCode);

        $position = null;
        foreach ($output as $line) {
            if (preg_match('/position\s*=\s*(\d+)/i', $line, $matches)) {
                $position = intval($matches[1]);
            }
        }

        if ($returnCode === 0 && $position !== null) {
            try {
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, position_capteur) VALUES (?, ?, ?)");
                $stmt->execute([$nom, $prenom, $position]);
                $message = "✅ Empreinte enregistrée pour $prenom $nom à la position #$position";
            } catch (PDOException $e) {
                $message = "❌ Erreur BDD : " . $e->getMessage();
            }
        } else {
            $message = "❌ Impossible de récupérer la position.";
        }
    } else {
        $message = "❌ Merci de remplir tous les champs.";
    }
}
?>

<h1 class="mb-4">👤 Enregistrement d’une empreinte</h1>

<?php if (!empty($message)): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST" class="row g-3">
    <div class="col-md-4">
        <label for="nom" class="form-label">Nom</label>
        <input type="text" name="nom" id="nom" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label for="prenom" class="form-label">Prénom</label>
        <input type="text" name="prenom" id="prenom" class="form-control" required>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">💾 Scanner & Enregistrer</button>
    </div>
</form>

<?php include 'footer.php'; ?>
