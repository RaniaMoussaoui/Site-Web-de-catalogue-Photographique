<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des données de la requête
$serviceId = $_GET['service_id'] ?? null;
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

// Validation des paramètres
if (!$serviceId || !$start || !$end) {
    die("Informations de réservation invalides.");
}

// Vérifier si le créneau est déjà réservé
$stmt = $pdo->prepare("
    SELECT * FROM reservations 
    WHERE id_service = :service_id 
    AND ((:start BETWEEN CONCAT(date_reservation, ' ', heure_debut) 
        AND CONCAT(date_reservation, ' ', heure_fin))
    OR (:end BETWEEN CONCAT(date_reservation, ' ', heure_debut) 
        AND CONCAT(date_reservation, ' ', heure_fin)))
");
$stmt->execute([
    ':service_id' => $serviceId,
    ':start' => $start,
    ':end' => $end,
]);

if ($stmt->rowCount() > 0) {
    $confirmationMessage = "Ce créneau est déjà réservé. Veuillez choisir un autre créneau.";
    $details = ["Message" => "Réservation impossible."];
} else {
    // Extraire les parties date, heure_debut et heure_fin
    $dateReservation = substr($start, 0, 10);
    $heureDebut = substr($start, 11, 5);
    $heureFin = substr($end, 11, 5);

    // Insérer la réservation
    $insertStmt = $pdo->prepare("
        INSERT INTO reservations (id_service, date_reservation, heure_debut, heure_fin) 
        VALUES (:service_id, :date_reservation, :heure_debut, :heure_fin)
    ");

    if ($insertStmt->execute([
        ':service_id' => $serviceId,
        ':date_reservation' => $dateReservation,
        ':heure_debut' => $heureDebut,
        ':heure_fin' => $heureFin,
    ])) {
        $confirmationMessage = "Réservation confirmée !";
        $details = [
            "Service" => $serviceId,
            "Date" => $dateReservation,
            "Heure de début" => $heureDebut,
            "Heure de fin" => $heureFin,
        ];
    } else {
        $confirmationMessage = "Erreur lors de la réservation.";
        $details = ["Message" => "Veuillez réessayer ou contacter le support."];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de réservation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 400px;
            width: 90%;
        }
        h1 {
            color:rgb(0, 0, 0);
            margin-bottom: 20px;
        }
        h1.error {
            color: #f44336;
        }
        p {
            margin: 10px 0;
        }
        .details {
            text-align: left;
            margin: 20px 0;
        }
        .details p {
            font-size: 14px;
            margin: 5px 0;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color:rgb(0, 0, 0);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .back-link:hover {
            background-color:rgb(0, 0, 0);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="<?= isset($details['Message']) ? 'error' : '' ?>"><?= htmlspecialchars($confirmationMessage) ?></h1>
        <div class="details">
            <?php foreach ($details as $key => $value): ?>
                <p><strong><?= htmlspecialchars($key) ?>:</strong> <?= htmlspecialchars($value) ?></p>
            <?php endforeach; ?>
        </div>
        <a href="acceuil.php" class="back-link">Retour à l'accueil</a>
    </div>
</body>
</html>
