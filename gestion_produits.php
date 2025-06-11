
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@400;500;700&display=swap");

        :root {
            --text-dark: #171717;
            --text-light: #525252;
            --extra-light: #a3a3a3;
            --white: #ffffff;
            --background: #f4f4f4;
            --green: #28a745;
            --red: #dc3545;
            --max-width: 1200px;
            --header-font: "Merriweather", serif;
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Montserrat", sans-serif;
            background-color: var(--background);
            color: var(--text-dark);
        }

        .container {
            max-width: var(--max-width);
            margin: auto;
            padding: 2rem 1rem;
        }

        h1 {
            font-family: var(--header-font);
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-card {
            max-width: 600px;
            margin: 0 auto 2rem auto; /* Centrer le formulaire et ajouter un espace en bas */
            background-color: var(--white);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        input, textarea {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid var(--extra-light);
            border-radius: 5px;
            font-size: 1rem;
        }

        input[type="file"] {
            padding: 0.75rem;
        }

        input:focus, textarea:focus {
            border-color: var(--text-dark);
            outline: none;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            font-size: 1rem;
            font-weight: 50;
            color: var(--white);
            background-color: var(--text-dark);
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 1rem;
        }

        .btn:hover {
            background-color: var(--text-light);
        }

        .products {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .product-card {
            flex: 1 1 calc(25% - 1rem); /* Chaque carte occupe 20% de largeur */
            max-width: calc(25% - 1rem); /* Maintient une taille uniforme */
            background-color: var(--white);
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            display: flex;
            flex-direction: column;
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .price {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .price-original {
            text-decoration: line-through;
            color: var(--extra-light);
        }

        .price-new {
            font-size: 1.5rem;
            color: green;
            margin-top: 10px;
        }
        .reduction {
        font-size: 1rem;
        color: var(--extra-light);
        margin-left: 10px;
        }


        .discount {
        color: red;
        margin-left: 10px;
        }

        .actions {
        display: flex;
        justify-content: center;
        gap: 0.9 rem;
        margin-top: 1rem;
        }
        

        .btn-modifier, .btn-supprimer {
        flex: 0.9; 
        padding: 0.9rem 1rem; 
        font-size: 0.9rem; 
        }

        .btn-modifier {
        background-color: var(--text-dark);
        }

        .btn-modifier:hover {
        background-color: var(--text-light);
        }

        .btn-supprimer {
        background-color: var(--text-dark);
        }

        .btn-supprimer:hover {
        background-color: var(--text-light);
        }

    </style>



<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fonction pour mettre à jour la moyenne des notes
function updateMoyenneNote($pdo, $idProduit) {
    $query = "
        UPDATE produits
        SET moyenneNote = (
            SELECT COALESCE(AVG(rating), 0)
            FROM evaluations
            WHERE idProduit = :idProduit
        )
        WHERE idProduit = :idProduit
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':idProduit' => $idProduit]);
}

// Traitement des actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter'])) {
        try {
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $prix = $_POST['prix'];
            $quantiteStock = $_POST['quantiteStock'];
            $pourcentage = !empty($_POST['pourcentage']) ? $_POST['pourcentage'] : null;

            $prixApresRemise = $prix;
            if (!empty($pourcentage)) {
                $prixApresRemise = $prix * (1 - $pourcentage / 100);
            }

            $imageURL = '';
            if (isset($_FILES['imageURL']) && $_FILES['imageURL']['error'] === UPLOAD_ERR_OK) {
                $imageTmpPath = $_FILES['imageURL']['tmp_name'];
                $imageName = uniqid() . '-' . $_FILES['imageURL']['name'];
                $imageURL = 'uploads/' . $imageName;
                move_uploaded_file($imageTmpPath, $imageURL);
            }

            $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, quantiteStock, imageURL, pourcentage, prixApresRemise) 
                                   VALUES (:nom, :description, :prix, :quantiteStock, :imageURL, :pourcentage, :prixApresRemise)");
            $stmt->execute([
                ':nom' => $nom,
                ':description' => $description,
                ':prix' => $prix,
                ':quantiteStock' => $quantiteStock,
                ':imageURL' => $imageURL,
                ':pourcentage' => $pourcentage,
                ':prixApresRemise' => $prixApresRemise
            ]);
            $message = "Produit ajouté avec succès!";
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    }

    if (isset($_POST['modifier'])) {
        try {
            $idProduit = $_POST['idProduit'];
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $prix = $_POST['prix'];
            $quantiteStock = $_POST['quantiteStock'];
            $pourcentage = !empty($_POST['pourcentage']) ? $_POST['pourcentage'] : null;

            $prixApresRemise = $prix;
            if (!empty($pourcentage)) {
                $prixApresRemise = $prix * (1 - $pourcentage / 100);
            }

            $stmt = $pdo->prepare("UPDATE produits 
                                   SET nom = :nom, description = :description, prix = :prix, quantiteStock = :quantiteStock, pourcentage = :pourcentage, prixApresRemise = :prixApresRemise
                                   WHERE idProduit = :idProduit");
            $stmt->execute([
                ':nom' => $nom,
                ':description' => $description,
                ':prix' => $prix,
                ':quantiteStock' => $quantiteStock,
                ':pourcentage' => $pourcentage,
                ':prixApresRemise' => $prixApresRemise,
                ':idProduit' => $idProduit
            ]);

            $message = "Produit modifié avec succès!";
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    }
}

if (isset($_GET['supprimer'])) {
    $idProduit = $_GET['supprimer'];
    try {
        $stmt = $pdo->prepare("DELETE FROM produits WHERE idProduit = :idProduit");
        $stmt->execute([':idProduit' => $idProduit]);
        $message = "Produit supprimé avec succès!";
    } catch (Exception $e) {
        $message = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Mettre à jour la moyenne pour tous les produits (optionnel)
$stmtProduits = $pdo->query("SELECT idProduit FROM produits");
$produitsIds = $stmtProduits->fetchAll(PDO::FETCH_COLUMN);
foreach ($produitsIds as $idProduit) {
    updateMoyenneNote($pdo, $idProduit);
}

// Récupération des produits
$stmt = $pdo->query("SELECT * FROM produits ORDER BY moyenneNote DESC");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération du produit à modifier
$produitAModifier = null;
if (isset($_GET['modifier'])) {
    $idProduit = $_GET['modifier'];
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE idProduit = :idProduit");
    $stmt->execute([':idProduit' => $idProduit]);
    $produitAModifier = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits</title>
    <link rel="stylesheet" href="styles.css"> <!-- Inclure votre style ici -->
</head>
<body>
<div class="container">
    <h1>Gestion des Produits</h1>

    <!-- Message de confirmation -->
    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <!-- Formulaire de modification ou ajout -->
    <div class="form-card">
        <form method="POST" enctype="multipart/form-data">
            <?php if ($produitAModifier): ?>
                <input type="hidden" name="idProduit" value="<?= $produitAModifier['idProduit'] ?>">
                <input type="text" name="nom" placeholder="Nom du produit" value="<?= $produitAModifier['nom'] ?>" required>
                <textarea name="description" placeholder="Description" required><?= $produitAModifier['description'] ?></textarea>
                <input type="number" name="prix" placeholder="Prix" step="0.01" value="<?= $produitAModifier['prix'] ?>" required>
                <input type="number" name="quantiteStock" placeholder="Quantité en stock" value="<?= $produitAModifier['quantiteStock'] ?>" required>
                <input type="number" name="pourcentage" placeholder="Pourcentage de réduction" value="<?= $produitAModifier['pourcentage'] ?>">
                <input type="file" name="imageURL">
                <button type="submit" name="modifier" class="btn">Modifier le produit</button>
            <?php else: ?>
                <input type="text" name="nom" placeholder="Nom du produit" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="number" name="prix" placeholder="Prix" step="0.01" required>
                <input type="number" name="quantiteStock" placeholder="Quantité en stock" required>
                <input type="number" name="pourcentage" placeholder="Pourcentage de réduction (laisser vide si aucune)">
                <input type="file" name="imageURL" required>
                <button type="submit" name="ajouter" class="btn">Ajouter le produit</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- Liste des produits -->
    <div class="products">
        <?php foreach ($produits as $produit): ?>
            <div class="product-card">
                <img src="<?= $produit['imageURL'] ?>" alt="<?= $produit['nom'] ?>" class="product-image">
                <h3><?= $produit['nom'] ?></h3>
                <p><?= $produit['description'] ?></p>
                <p>Prix après remise : <strong><?= number_format($produit['prixApresRemise'], 2) ?> €</strong></p>
                
                <div class="actions">
                    <a href="?modifier=<?= $produit['idProduit'] ?>" class="btn btn-modifier">Modifier</a>
                    <form method="POST" action="?supprimer=<?= $produit['idProduit'] ?>">
                        <button type="submit" class="btn btn-supprimer">Supprimer</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
