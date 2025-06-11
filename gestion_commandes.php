<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Traitement de la mise à jour du statut
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    try {
        $id = $_POST['idCommande'];
        $statut = $_POST['statut'];
        // Déterminer le message à envoyer en fonction du statut
        $notification = '';
        if ($statut === 'En préparation') {
            $notification = 'Votre commande est en préparation';
        } elseif ($statut === 'Envoyé') {
            $notification = 'Votre commande a été envoyée';
        } elseif ($statut === 'Livré') {
            $notification = 'Votre commande a été livrée';
        }

        // Mise à jour du statut de la commande avec notification
        $stmt = $pdo->prepare("UPDATE commandes SET statut = :statut, notification = :notification WHERE idCommande = :idCommande");
        $stmt->execute([':statut' => $statut, ':notification' => $notification, ':idCommande' => $id]);
        
        $message = "Statut de la commande mis à jour avec succès!";
    } catch (Exception $e) {
        $message = "Erreur lors de la mise à jour du statut: " . $e->getMessage();
    }
}

// Traitement de la suppression de la commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
    try {
        $id = $_POST['idCommande'];
        // Suppression de la commande
        $stmt = $pdo->prepare("DELETE FROM commandes WHERE idCommande = :idCommande");
        $stmt->execute([':idCommande' => $id]);
        $message = "Commande supprimée avec succès!";
    } catch (Exception $e) {
        $message = "Erreur lors de la suppression de la commande: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .message {
            color: green;
            text-align: center;
            font-size: 1.2rem;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #333;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #555;
        }
        .form-group {
            margin-bottom: 20px;
        }
        select {
            padding: 10px;
            font-size: 1rem;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Gestion des Commandes</h1>

    <!-- Message de confirmation -->
    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Tableau des commandes -->
    <table>
        <thead>
            <tr>
                <th>ID Commande</th>
                <th>ID Client</th>
                <th>Montant Total</th>
                <th>Statut</th>
                <th>Notification</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Récupération des commandes
            $stmt = $pdo->query("SELECT * FROM commandes ORDER BY idCommande DESC");
            $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($commandes as $commande): ?>
                <tr>
                    <td><?= htmlspecialchars($commande['idCommande']) ?></td>
                    <td><?= htmlspecialchars($commande['idClient']) ?></td>
                    <td><?= number_format($commande['montantTotal'], 2) ?> €</td>
                    <td><?= htmlspecialchars($commande['statut']) ?></td>
                    <td><?= htmlspecialchars($commande['notification']) ?></td>
                    <td class="actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="idCommande" value="<?= $commande['idCommande'] ?>">
                            <select name="statut">
                                <option value="En préparation" <?= $commande['statut'] == 'En préparation' ? 'selected' : '' ?>>En préparation</option>
                                <option value="Envoyé" <?= $commande['statut'] == 'Envoyé' ? 'selected' : '' ?>>Envoyé</option>
                                <option value="Livré" <?= $commande['statut'] == 'Livré' ? 'selected' : '' ?>>Livré</option>
                            </select>
                            <button type="submit" name="update_status" class="btn">Mettre à jour</button>
                        </form>

                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="idCommande" value="<?= $commande['idCommande'] ?>">
                            <button type="submit" name="supprimer" class="btn">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
