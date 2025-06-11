<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=visiora_db;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

// Vérifie si un client est sélectionné
$clientId = isset($_GET['clientId']) ? intval($_GET['clientId']) : null;

// Récupère la liste des clients
$queryClients = "SELECT id, nom, email FROM client";
$stmtClients = $pdo->prepare($queryClients);
$stmtClients->execute();
$clients = $stmtClients->fetchAll(PDO::FETCH_ASSOC);

// Si un client est sélectionné, récupère son historique
$historique = [];
if ($clientId) {
    $queryHistorique = "
        SELECT 'Commande' AS Type, commandes.dateCommande AS Date, commandes.montantTotal AS Détail
        FROM commandes
        WHERE commandes.idClient = :clientId
        UNION ALL
        SELECT 'Message' AS Type, messages.dateEnvoi AS Date, messages.message AS Détail
        FROM messages
        WHERE messages.idClient = :clientId
        UNION ALL
        SELECT 'Panier' AS Type, NOW() AS Date, CONCAT('Produit ID: ', panier.idProduit, ', Quantité: ', panier.quantite) AS Détail
        FROM panier
        WHERE panier.idClient = :clientId
        UNION ALL

    ";
    $stmtHistorique = $pdo->prepare($queryHistorique);
    $stmtHistorique->execute([':clientId' => $clientId]);
    $historique = $stmtHistorique->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste et historique des clients</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        form {
            margin: 20px 0;
        }
        select, button {
            padding: 10px;
            font-size: 16px;
            margin: 10px 0;
        }
        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Liste des clients</h1>
    <form action="" method="GET">
        <label for="client">Sélectionnez un client :</label>
        <select name="clientId" id="client" required>
            <option value="">-- Sélectionner un client --</option>
            <?php foreach ($clients as $client): ?>
                <option value="<?= htmlspecialchars($client['id']) ?>" <?= $clientId == $client['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($client['nom']) ?> (<?= htmlspecialchars($client['email']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Afficher l'historique</button>
    </form>

    <?php if ($clientId): ?>
        <h2>Historique des activités</h2>
        <?php if (count($historique) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Détail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['Type']) ?></td>
                            <td><?= htmlspecialchars($item['Date']) ?></td>
                            <td><?= htmlspecialchars($item['Détail']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune activité trouvée pour ce client.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
