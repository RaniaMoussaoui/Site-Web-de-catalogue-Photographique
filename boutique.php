<?php
session_start();

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer les produits
$stmt = $pdo->query("SELECT * FROM produits");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique - Visiora</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav>
            <a href="acceuil.php">Accueil</a>
            <a href="panier.php">Panier</a>
        </nav>
    </header>

    <section class="boutique" id="boutique">
        <div class="section__container boutique__container">
            <h2 class="section__header">~ BOUTIQUE ~</h2>
            <p class="section__description">Découvrez nos produits exclusifs en vente !</p>

            <div class="boutique__grid">
                <?php foreach ($produits as $produit): ?>
                    <div class="boutique__card">
                        <img src="<?= htmlspecialchars($produit['imageURL']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" />
                        <div class="boutique__content">
                            <h3><?= htmlspecialchars($produit['nom']) ?></h3>
                            <p><?= htmlspecialchars($produit['description']) ?></p>

                            <!-- Section pour afficher les étoiles -->
                            <div class="product-rating" data-product-id="<?= $produit['idProduit'] ?>">
                                <?php
                                $stmt = $pdo->prepare('SELECT AVG(rating) AS average_rating FROM evaluations WHERE idProduit = :idProduit');
                                $stmt->execute(['idProduit' => $produit['idProduit']]);
                                $averageRating = $stmt->fetchColumn();
                                $averageRating = round($averageRating, 1);

                                for ($i = 1; $i <= 5; $i++) {
                                    $starClass = ($i <= $averageRating) ? 'star filled' : 'star';
                                    echo "<span class='$starClass' data-value='$i'>&#9733;</span>";
                                }
                                ?>
                            </div>

                            <div class="boutique__price">
                                <div class="prix-inline">
                                    <?php if ($produit['pourcentage'] > 0): ?>
                                        <span class="prix-original"><?= number_format($produit['prix'], 2) ?> €</span>
                                        <span class="pourcentage-reduction">-<?= $produit['pourcentage'] ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <span class="prix-remise"><?= number_format($produit['prix'] - ($produit['prix'] * $produit['pourcentage'] / 100), 2) ?> €</span>
                            </div>

                            <!-- Ajouter un produit au panier -->
                            <div class="boutique__cta">
                                <button class="btn--black add-to-cart" data-product-id="<?= $produit['idProduit'] ?>" data-user-id="<?= $_SESSION['user_id'] ?>">
                                    Ajouter au panier
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer>
    <p>&copy; 2025 Visiora Photographie. Tous droits réservés.</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const addToCartButtons = document.querySelectorAll('.add-to-cart');

            addToCartButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.getAttribute('data-product-id');
                    const userId = this.getAttribute('data-user-id');

                    // Préparation de la requête AJAX
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'panier.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                alert(response.message);
                            } else {
                                alert('Erreur : ' + response.message);
                            }
                        } else {
                            alert('Une erreur est survenue lors de l\'ajout au panier.');
                        }
                    };

                    // Envoi des données au serveur
                    xhr.send('action=ajouter&product_id=' + productId + '&user_id=' + userId);
                });
            });
        });
    </script>
</body>
</html>


<style>
        /* Styles pour la page boutique */
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

    .section__container {
        width: 90%;
        margin: 0 auto;
        padding: 40px 0;
    }

    .boutique__container {
        text-align: center;
    }

    .boutique__grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);  
        gap: 20px;
        margin-top: 20px;
    }

    .boutique__card {
        background-color: white;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .boutique__card:hover {
        transform: translateY(-5px);
    }

    .boutique__card img {
        width: 100%;
        height: 180px; /* Réduit la hauteur de l'image */
        object-fit: cover;
        border-radius: 5px;
    }

    .boutique__content {
        padding: 10px;
    }

    .boutique__content h3 {
        font-size: 1.1em;
        margin: 5px 0;
    }

    .boutique__content p {
        font-size: 0.9em;
        color: #555;
        margin: 5px 0;
    }

    .boutique__price {
        margin: 10px 0;
    }

    .prix-original {
        text-decoration: line-through;
        color: #888;
        margin-right: 10px;
    }

    .pourcentage-reduction {
        color: red;
        font-weight: bold;
    }

    .prix-remise {
        color: green;
        font-weight: bold;
        font-size: 1.2em;
    }

    .boutique__cta {
        margin-top: 10px;
    }

    .boutique__cta .btn--black {
        background-color: #333;
        color: white;
        padding: 6px 15px;
        border: none;
        cursor: pointer;
        font-size: 0.8em;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .boutique__cta .btn--black:hover {
        background-color: #555;
    }

    .product-rating {
        display: inline-block;
        margin-top: 10px;
        cursor: pointer;
    }

    .star {
        font-size: 1.5em;
        color: #ddd;
        transition: color 0.3s ease;
    }

    .star.filled {
        color: gold;
    }

    .star:hover {
        color: orange;
    }

/* Footer */
footer {
        background-color: #333;
        color: white;
        text-align: center;
        padding: 20px;
    }

    footer p {
        margin: 0;
        font-size: 1em;
    }
</style>