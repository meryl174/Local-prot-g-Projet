<?php
include 'config.php';

// On récupère les accès non clôturés et sans alerte envoyée
$stmt = $pdo->query("
    SELECT a.id, a.date_heure, u.nom, u.email
    FROM acces a
    JOIN utilisateurs u ON a.utilisateur_id = u.id
    WHERE a.duree IS NULL AND (a.alerte_envoyee IS NULL OR a.alerte_envoyee = 0)
");

$alertes = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $date_heure = strtotime($row['date_heure']);
    $maintenant = time();

    if (($maintenant - $date_heure) >= 900) { // 900 secondes = 15 minutes
        // ➤ ENVOI EMAIL (simple)
        $to = "admin@example.com"; // ou $row['email'] si tu veux envoyer à l'utilisateur
        $subject = "⚠️ Alerte: session non clôturée";
        $message = "L'utilisateur " . $row['nom'] . " est entré depuis plus de 15 minutes sans sortie (ID session #$id)";
        $headers = "From: alertes@tonsite.com";

        mail($to, $subject, $message, $headers);

        // ➤ Optionnel : ENVOI SMS via API externe (ex: Twilio, OVH, Free, etc.)

        // ➤ Mettre à jour la session comme déjà alertée
        $update = $pdo->prepare("UPDATE acces SET alerte_envoyee = 1 WHERE id = ?");
        $update->execute([$id]);

        $alertes++;
    }
}

echo "✅ Vérification terminée. $alertes alerte(s) envoyée(s).";
