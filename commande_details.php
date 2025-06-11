<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer l'idCommande depuis la requête AJAX
if (isset($_POST['idCommande'])) {
    $idCommande = $_POST['idCommande'];

    // Fonction pour récupérer les produits d'une commande
    function getCommandeDetails($pdo, $idCommande)
    {
        // Assurez-vous que la colonne 'image' existe dans la table 'produits'
        $stmt = $pdo->prepare("
            SELECT p.idProduit, p.nom, p.imageURL, pc.quantite, p.prixApresRemise
            FROM produits_commandes pc
            JOIN produits p ON p.idProduit = pc.idProduit
            WHERE pc.idCommande = :idCommande
        ");
        $stmt->execute([':idCommande' => $idCommande]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les détails des produits pour la commande donnée
    $produits = getCommandeDetails($pdo, $idCommande);

    // Afficher les détails des produits
if (count($produits) > 0) {
    echo '<table>';
    echo '<tr><th>Image</th><th>Produit</th><th>Quantité</th><th>Prix Unitaire</th><th>Total</th></tr>';
    foreach ($produits as $produit) {
        echo '<tr>';
        // Affichage de l'image du produit
        echo '<td><img src="' . htmlspecialchars($produit['imageURL']) . '" alt="' . htmlspecialchars($produit['nom']) . '" style="width: 80px; height: auto;"></td>';
        // Affichage des autres détails
        echo '<td>' . htmlspecialchars($produit['nom']) . '</td>';
        echo '<td>' . htmlspecialchars($produit['quantite']) . '</td>';
        echo '<td>' . htmlspecialchars(number_format($produit['prixApresRemise'], 2)) . ' €</td>';
        echo '<td>' . htmlspecialchars(number_format($produit['prixApresRemise'] * $produit['quantite'], 2)) . ' €</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>Aucun produit trouvé pour cette commande.</p>';
}

}
?>

    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Styles pour la popup */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 600px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            z-index: 1000;
        }
        .popup .close {
            display: block;
            text-align: right;
            margin-bottom: 10px;
        }
        .popup .close button {
            background: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .product-card {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .product-card img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
            border-radius: 5px;
        }
        .product-card div {
            flex: 1;
        }
    </style>
