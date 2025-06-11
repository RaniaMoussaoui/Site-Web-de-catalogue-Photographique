<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier si un utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter pour accéder au paiement.");
}

$idClient = $_SESSION['user_id'];

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

$commandeReussie = false;  // Variable pour indiquer si la commande a été réussie
$orderId = 0;  // Variable pour l'ID de la commande

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paiement'])) {
    // Traitement de la commande et simulation de paiement
    $nomClient = $_POST['nom'];
    $adresse = $_POST['adresse'];
    $numeroCarte = $_POST['carte'];

    // Simuler une validation de paiement
    if ($numeroCarte) {
        // Enregistrer la commande dans la base de données
        $stmt = $pdo->prepare('
            INSERT INTO commandes (idClient, montantTotal, dateCommande)
            VALUES (:idClient, :montantTotal, NOW())
        ');
        $stmt->execute(['idClient' => $idClient, 'montantTotal' => $total]);

        // Récupérer l'ID de la commande
        $orderId = $pdo->lastInsertId();

        // Ajouter les produits de la commande dans la table 'produits_commandes'
        foreach ($panier as $item) {
            $stmt = $pdo->prepare('
                INSERT INTO produits_commandes (idCommande, idProduit, quantite)
                VALUES (:idCommande, :idProduit, :quantite)
            ');
            $stmt->execute([
                'idCommande' => $orderId,
                'idProduit' => $item['idProduit'],
                'quantite' => $item['quantite']
            ]);
        }

        // Vider le panier
        $stmt = $pdo->prepare('DELETE FROM panier WHERE idClient = :idClient');
        $stmt->execute(['idClient' => $idClient]);

        // Commande réussie
        $commandeReussie = true;
    } else {
        $errorMessage = "Veuillez entrer un numéro de carte valide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - Visiora</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav>
            <a href="acceuil.php">Accueil</a>
            <a href="boutique.php">Boutique</a>
            <a href="panier.php">Panier</a>
        </nav>
    </header>

    <section class="panier">
        <?php if (!$commandeReussie): ?>
            <h2>Paiement</h2>
            <?php if (count($panier) > 0): ?>
                <p>Total à payer : <?= number_format($total, 2) ?> €</p>
                
                <form method="POST">
                    <div>
                        <label for="nom">Nom :</label>
                        <input type="text" name="nom" id="nom" required>
                    </div>
                    <div>
                        <label for="adresse">Adresse :</label>
                        <input type="text" name="adresse" id="adresse" required>
                    </div>
                    <div>
                        <label for="carte">Numéro de carte :</label>
                        <input type="text" name="carte" id="carte" required>
                    </div>
                    <button type="submit" name="paiement" class="btn--black">Payer</button>
                </form>
                <?php if (isset($errorMessage)): ?>
                    <p style="color: red;"><?= $errorMessage ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p>Votre panier est vide.</p>
            <?php endif; ?>
        <?php else: ?>
            <h2>Commande Confirmée</h2>
            <p>Merci pour votre commande !</p>
            <p>Numéro de commande : <?= $orderId ?></p>
            <p>Total payé : <?= number_format($total, 2) ?> €</p>
            <p>Nous vous enverrons un email de confirmation sous peu.</p>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; 2025 Visiora Photographie. Tous droits réservés.</p>
    </footer>
</body>
</html>

<style>
    /* Styles pour la page Paiement */
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

/* Section Paiement */
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

.panier p {
    font-size: 1.2em;
    text-align: center;
    margin-bottom: 20px;
}

/* Formulaire Paiement */
form {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

form div {
    margin-bottom: 15px;
}

form label {
    font-size: 1.1em;
    margin-bottom: 5px;
    display: block;
}

form input[type="text"] {
    width: 100%;
    padding: 10px;
    font-size: 1em;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
    color: #333;
}

form button[type="submit"] {
    background-color: #333;
    color: white;
    padding: 10px 20px;
    font-size: 1.1em;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: block;
    width: 100%;
    margin-top: 20px;
}

form button[type="submit"]:hover {
    background-color: #555;
}

/* Confirmation de commande */
.panier .commande-confirmee {
    text-align: center;
    margin-top: 30px;
}

.panier .commande-confirmee p {
    font-size: 1.2em;
    color: #5cb85c; /* Vert pour la confirmation */
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
