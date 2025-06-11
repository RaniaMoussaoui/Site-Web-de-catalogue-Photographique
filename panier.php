<?php
session_start();

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier si un utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter pour accéder à votre panier.");
}

$idClient = $_SESSION['user_id'];

// Ajouter un produit au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $idProduit = (int)$_POST['product_id'];
    $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 1;

    if ($_POST['action'] === 'ajouter') {
        // Vérifier si le produit est déjà dans le panier
        $stmt = $pdo->prepare('SELECT * FROM panier WHERE idProduit = :idProduit AND idClient = :idClient');
        $stmt->execute(['idProduit' => $idProduit, 'idClient' => $idClient]);
        $produit = $stmt->fetch();

        if ($produit) {
            // Mettre à jour la quantité
            $stmt = $pdo->prepare('UPDATE panier SET quantite = quantite + :quantite WHERE idProduit = :idProduit AND idClient = :idClient');
            $stmt->execute(['quantite' => $quantite, 'idProduit' => $idProduit, 'idClient' => $idClient]);
        } else {
            // Ajouter un nouveau produit
            $stmt = $pdo->prepare('INSERT INTO panier (idProduit, idClient, quantite) VALUES (:idProduit, :idClient, :quantite)');
            $stmt->execute(['idProduit' => $idProduit, 'idClient' => $idClient, 'quantite' => $quantite]);
        }
    }

    // Supprimer un produit du panier
    if ($_POST['action'] === 'supprimer') {
        $stmt = $pdo->prepare('DELETE FROM panier WHERE idProduit = :idProduit AND idClient = :idClient');
        $stmt->execute(['idProduit' => $idProduit, 'idClient' => $idClient]);
    }

    // Rediriger après traitement pour éviter la double soumission du formulaire
    header('Location: panier.php');
    exit;
}

// Récupérer les produits du panier
$stmt = $pdo->prepare('
    SELECT p.idProduit, p.nom, p.prixApresRemise, pa.quantite 
    FROM panier pa
    INNER JOIN produits p ON pa.idProduit = p.idProduit
    WHERE pa.idClient = :idClient
');
$stmt->execute(['idClient' => $idClient]);
$panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculer le total
$total = 0;
foreach ($panier as $item) {
    $total += $item['prixApresRemise'] * $item['quantite'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - Visiora</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <nav>
        <a href="acceuil.php">Accueil</a>
        <a href="boutique.php">Boutique</a>

    </nav>
</header>

<section class="panier">
    <h2>Votre Panier</h2>
    <?php if (count($panier) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Sous-total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($panier as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nom']) ?></td>
                        <td><?= number_format($item['prixApresRemise'], 2) ?> €</td>
                        <td><?= $item['quantite'] ?></td>
                        <td><?= number_format($item['prixApresRemise'] * $item['quantite'], 2) ?> €</td>
                        <td>
                            <form action="panier.php" method="POST" style="display: inline-block;">
                                <input type="hidden" name="product_id" value="<?= $item['idProduit'] ?>">
                                <input type="hidden" name="action" value="supprimer">
                                <button type="submit" class="btn--delete">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total">
            <p>Total : <?= number_format($total, 2) ?> €</p>
        </div>
        <button class="btn--black" onclick="window.location.href='paiement.php';">Passer à la caisse</button>

        
    <?php else: ?>
        <p>Votre panier est vide.</p>
    <?php endif; ?>
</section>

<footer>
    <p>&copy; 2025 Visiora Photographie. Tous droits réservés.</p>
</footer>
</body>
</html>

<style>
    
    /* Styles pour la page Panier */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    header {
        background-color: #333;
        color: white;
        padding: 10px;
        text-align: center;
    }

    header nav {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    header nav a {
        color: white;
        text-decoration: none;
    }

    header nav a:hover {
        text-decoration: underline;
    }

    /* Section Panier */
    .panier {
        width: 90%;
        margin: 20px auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    .panier h2 {
        font-size: 1.5em;
        text-align: center;
        margin-bottom: 20px;
    }

    /* Table Panier */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th, table td {
        padding: 15px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    table th {
        background-color: #f8f8f8;
    }

    table td {
        background-color: #fff;
    }

    /* Boutons */
    .btn--delete {
        background-color: #d9534f;
        color: white;
        border: none;
        padding: 6px 15px;
        cursor: pointer;
        font-size: 0.9em;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .btn--delete:hover {
        background-color: #c9302c;
    }

    .btn--black {
        background-color: #333;
        color: white;
        padding: 10px 20px;
        font-size: 1.1em;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: block;
        width: 200px;
        margin: 20px auto;
    }

    .btn--black:hover {
        background-color: #555;
    }

    /* Total */
    .total {
        text-align: right;
        font-weight: bold;
        font-size: 1.2em;
        margin-top: 20px;
    }

    /* Footer */
    footer {
        background-color: #333;
        color: white;
        text-align: center;
        padding: 20px;
    }

/* Footer */
footer {
        background-color: #111;
        color: white;
        padding: 10px;
        text-align: center;
        position: fixed;
        bottom: 0;
        width: 100%;
    }
</style>