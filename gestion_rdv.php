<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les réservations de l'administration
$stmt = $pdo->prepare("SELECT r.id, r.date_reservation, r.heure_debut, r.heure_fin, s.nom AS service
                       FROM reservations r
                       JOIN services s ON r.id_service = s.idService
                       ORDER BY r.date_reservation DESC");
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Suppression d'une réservation
if (isset($_GET['delete_id'])) {
    $deleteStmt = $pdo->prepare("DELETE FROM reservations WHERE id = :id");
    $deleteStmt->execute([':id' => $_GET['delete_id']]);
    header("Location: gestion_rdv.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réservations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #000;
            color: white;
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .btn {
            padding: 5px 10px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-delete {
            background-color: #000;
        }
        .btn-delete:hover {
            background-color: #000;
        }
        .btn-edit {
            background-color: #000;
        }
        .btn-edit:hover {
            background-color: #000;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #000;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            width: 100%;
        }
        .back-link:hover {
            background-color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Réservations</h1>
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Date de Réservation</th>
                    <th>Heure de Début</th>
                    <th>Heure de Fin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><?= htmlspecialchars($reservation['service']) ?></td>
                        <td><?= htmlspecialchars($reservation['date_reservation']) ?></td>
                        <td><?= htmlspecialchars($reservation['heure_debut']) ?></td>
                        <td><?= htmlspecialchars($reservation['heure_fin']) ?></td>
                        <td class="actions">
                            <!-- Bouton de suppression -->
                            <a href="?delete_id=<?= $reservation['id'] ?>" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?')">Supprimer</a>

                            <!-- Bouton d'édition (pour le futur) -->
                            <a href="gestion_rdv.php?id=<?= $reservation['id'] ?>" class="btn btn-edit">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="admin_acceuil.html" class="back-link">Retour à l'accueil</a>
    </div>
</body>
</html>
