<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier si l'utilisateur est connecté
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

// Récupérer l'ID de l'utilisateur connecté
$userId = $_SESSION['user_id'];

// Récupérer les commandes de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE idClient = :user_id ORDER BY dateCommande DESC");
$stmt->execute([':user_id' => $userId]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour récupérer les produits commandés pour une commande donnée
function getCommandeDetails($pdo, $idCommande)
{
    // Requête pour récupérer les produits et leurs informations pour une commande donnée
    $stmt = $pdo->prepare("
        SELECT p.idProduit, p.nomProduit, pc.quantite, p.prix
        FROM produits_commandes pc
        JOIN produits p ON p.idProduit = pc.idProduit
        WHERE pc.idCommande = :idCommande
    ");
    $stmt->execute([':idCommande' => $idCommande]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes</title>
    <style>
        /* Styles similaires à ceux que vous avez déjà ajoutés */
    </style>
</head>
<body>
<div class="container">
    <h1>Mes Commandes</h1>

    <?php if (count($commandes) > 0): ?>
        <table>
            <thead>
            <tr>
                <th>ID Commande</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Notification</th>
                <th>Total</th>
                <th>Détails</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($commandes as $commande): ?>
                <tr>
                    <td><?= htmlspecialchars($commande['idCommande']) ?></td>
                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($commande['dateCommande']))) ?></td>
                    <td class="status <?= strtolower(str_replace(' ', '-', $commande['statut'])) ?>">
                        <?= htmlspecialchars($commande['statut']) ?>
                    </td>
                    <td class="notification <?= strtolower(str_replace(' ', '-', $commande['notification'])) ?>">
                        <?= htmlspecialchars($commande['notification']) ?>
                    </td>
                    <td><?= htmlspecialchars(number_format($commande['montantTotal'], 2)) ?> €</td>
                    <td>
                        <button class="btn-details" onclick="showDetails(<?= $commande['idCommande'] ?>)">Voir</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Vous n'avez aucune commande pour le moment.</p>
    <?php endif; ?>
</div>

<!-- Popup pour afficher les détails -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modalBody">
            <!-- Les détails de la commande seront chargés ici -->
        </div>
    </div>
</div>

<script>
    function showDetails(idCommande) {
        // Afficher la popup
        const modal = document.getElementById('detailsModal');
        const modalBody = document.getElementById('modalBody');
        modal.style.display = 'block';

        // Charger les détails via AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'commande_details.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (this.status === 200) {
                modalBody.innerHTML = this.responseText;
            } else {
                modalBody.innerHTML = '<p>Erreur lors du chargement des détails.</p>';
            }
        };
        xhr.send('idCommande=' + idCommande);
    }

    function closeModal() {
        document.getElementById('detailsModal').style.display = 'none';
    }
</script>
</body>
</html>




<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f4f4f4;
            color: #333;
        }

        .status {
            font-weight: bold;
            text-transform: capitalize;
        }

        .status.en-preparation {
            color: orange;
        }

        .status.envoye {
            color: blue;
        }

        .status.livre {
            color: green;
        }

        .btn-details {
            padding: 5px 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-details:hover {
            background-color: #555;
        }

        /* Styles pour la popup */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }
    </style>