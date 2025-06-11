<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération du service sélectionné
$serviceId = $_GET['service_id'] ?? null;

if (!$serviceId) {
    die("Service introuvable.");
}

// Récupération des informations du service
$stmt = $pdo->prepare("SELECT nom, duree FROM services WHERE idService = :id");
$stmt->execute([':id' => $serviceId]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    die("Service introuvable.");
}

$duree = (int)$service['duree']; // Durée de la prestation en minutes

// Récupération des réservations pour ce service
$reservationsStmt = $pdo->prepare("
    SELECT date_reservation, heure_debut, heure_fin 
    FROM reservations 
    WHERE id_service = :id_service
");
$reservationsStmt->execute([':id_service' => $serviceId]);
$reservations = $reservationsStmt->fetchAll(PDO::FETCH_ASSOC);

// Génération des créneaux horaires
$horaireDebut = new DateTime('09:00');
$horaireFin = new DateTime('18:00');
$interval = new DateInterval('PT' . $duree . 'M'); // Intervalle basé sur la durée
$horaireActuel = clone $horaireDebut;
$creneaux = [];

// Générer les créneaux
while ($horaireActuel < $horaireFin) {
    $creneauDebut = clone $horaireActuel;
    $horaireActuel->add($interval);
    if ($horaireActuel <= $horaireFin) {
        $creneaux[] = [
            'start' => $creneauDebut->format('Y-m-d H:i'),
            'end' => $horaireActuel->format('Y-m-d H:i'),
        ];
    }
}

// Filtrer les créneaux pour exclure les réservations existantes
foreach ($reservations as $reservation) {
    $startReserved = $reservation['date_reservation'] . ' ' . $reservation['heure_debut'];
    $endReserved = $reservation['date_reservation'] . ' ' . $reservation['heure_fin'];
    $creneaux = array_filter($creneaux, function ($creneau) use ($startReserved, $endReserved) {
        return !(
            ($creneau['start'] >= $startReserved && $creneau['start'] < $endReserved) ||
            ($creneau['end'] > $startReserved && $creneau['end'] <= $endReserved)
        );
    });
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendrier - <?= htmlspecialchars($service['nom']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <style>
    body { font-family: Arial, sans-serif; }
    .container { max-width: 800px; margin: auto; padding: 20px; }
    h1 { text-align: center; }
  </style>
</head>
<body>
<div class="container">
  <h1>Calendrier pour : <?= htmlspecialchars($service['nom']) ?></h1>

  <div id="calendar"></div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'timeGridDay',
      slotMinTime: "09:00:00",
      slotMaxTime: "18:00:00",
      slotDuration: "00:<?= $duree ?>:00", // Durée des créneaux
      events: [
        <?php foreach ($creneaux as $creneau): ?>
        {
          title: "Disponible",
          start: "<?= htmlspecialchars($creneau['start']) ?>",
          end: "<?= htmlspecialchars($creneau['end']) ?>",
          color: "green" // Créneaux disponibles en vert
        },
        <?php endforeach; ?>
        <?php foreach ($reservations as $reservation): ?>
        {
          title: "Réservé",
          start: "<?= htmlspecialchars($reservation['date_reservation'] . 'T' . $reservation['heure_debut']) ?>",
          end: "<?= htmlspecialchars($reservation['date_reservation'] . 'T' . $reservation['heure_fin']) ?>",
          color: "red" // Créneaux réservés en rouge
        },
        <?php endforeach; ?>
      ],
      selectable: true,
      select: function(info) {
        const confirmation = confirm(
          `Voulez-vous réserver ce créneau du ${info.startStr} au ${info.endStr} ?`
        );
        if (confirmation) {
          window.location.href = `confirmation_rdv.php?service_id=<?= $serviceId ?>&start=${info.startStr}&end=${info.endStr}`;
        }
      }
    });

    calendar.render();
  });
</script>
</body>
</html>
