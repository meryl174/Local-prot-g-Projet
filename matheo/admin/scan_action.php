<?php
header("Content-Type: text/plain");

$nom = $_GET['nom'] ?? '';
$prenom = $_GET['prenom'] ?? '';

if (empty($nom) || empty($prenom)) {
    echo "⛔ Veuillez renseigner le nom et le prénom.";
    exit;
}

// Lancer le script Python
exec('/var/www/html/venv/bin/python3 /var/www/html/enregistrer_emp.py 2>&1', $output, $returnCode);

// Affiche toutes les instructions du script
foreach ($output as $line) {
    echo $line . "\n";
}

if ($returnCode !== 0) {
    echo "\n⛔ Le script Python a échoué (code $returnCode).\n";
    exit;
}

// Rechercher la position d'enregistrement
$position = -1;
foreach ($output as $line) {
    if (preg_match('/Empreinte enregistrée à la position #(\d+)/', $line, $matches)) {
        $position = intval($matches[1]);
        break;
    }
}

if ($position >= 0) {
    // Insérer l'utilisateur dans la base de données
    include 'config.php';
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, position_capteur) VALUES (?, ?, ?)");
    $stmt->execute([$nom, $prenom, $position]);
    echo "\n✅ Utilisateur \"$prenom $nom\" enregistré dans la base de données à la position #$position.";
} else {
    echo "\n⛔ Impossible d’obtenir la position de l’empreinte. L’utilisateur n’a pas été ajouté.";
}
?>

