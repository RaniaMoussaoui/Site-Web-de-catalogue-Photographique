<?php
session_start();

// Gestion de déconnexion
if (isset($_GET['deconnexion'])) {
    session_unset();
    session_destroy();
    header('Location: acceuil.php'); // Redirige après déconnexion
    exit;
}

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupération des services
$stmt = $pdo->query("SELECT * FROM services");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des produits
$stmt = $pdo->query("SELECT idProduit, nom, description, prix, quantiteStock, pourcentage, imageURL FROM produits");
$productStmt = $pdo->query("SELECT * FROM produits LIMIT 4");
$produits = $productStmt->fetchAll(PDO::FETCH_ASSOC);
// Récupérer les produits avec les meilleures notes (limité à 4 produits)
$stmt = $pdo->query("SELECT 
                        p.*, 
                        COALESCE(AVG(e.rating), 0) AS moyenneNote
                    FROM produits p
                    LEFT JOIN evaluations e ON p.idProduit = e.idProduit
                    GROUP BY p.idProduit
                    ORDER BY moyenneNote DESC
                    LIMIT 4");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Récupération des contacts
$stmt = $pdo->query("SELECT * FROM contacts");
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
    />
    <link rel="stylesheet" href="styles.css" />
    <title>Visiora Studio Photographie</title>
  </head>
  <body>
    <header class="header" id="home">
      <nav>
        <div class="nav__header">
          <div class="nav__logo">
            <a href="#">
              <img src="photographie/logo3.png" alt="logo Visiora" />
            </a>
          </div>
          <div class="nav__menu__btn" id="menu-btn">
            <i class="ri-menu-line"></i>
          </div>
        </div>
        <ul class="nav__links" id="nav-links">
  <li class="nav__logo">
    <a href="#">
      <img src="photographie/logo3.png" alt="logo Visiora" />
    </a>
  </li>
  <li><a href="#home">ACCUEIL</a></li>
  <li><a href="#about">À PROPOS</a></li>
  <li><a href="#galerie">GALERIE</a></li>
  <li><a href="#service">SERVICES</a></li>
  <li><a href="#boutique">BOUTIQUE</a></li>
  <li><a href="#avis">AVIS</a></li>
  <li><a href="#contact">CONTACT</a></li>
  <?php if (isset($_SESSION['user_id'])): ?>
    <li class="user-menu">
      <a href="#" class="user-toggle">MON COMPTE</a>
      <div class="dropdown-content">
        <a href="favoris.php">Favoris</a>
        <a href="mes_commandes.php">Mes Commandes</a>
        <a href="conf.php">Mes Réservations</a>
        <a href="?deconnexion=true">Déconnexion</a>
      </div>
    </li>
  <?php else: ?>
    <li><a href="connexion.php">CONNEXION</a></li>
  <?php endif; ?>
</ul>
<style>.user-menu {
    position: relative;
}

.user-toggle {
    display: inline-block;
    padding: 10px 15px;
    background-color: #000;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    cursor: pointer;
}

.user-toggle:hover {
    background-color: #333;
}

.dropdown-content {
    display: none;
    position: absolute;
    top: 40px;
    left: 0;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    z-index: 1000;
    overflow: hidden;
}

.dropdown-content a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: black;
    white-space: nowrap;
}

.dropdown-content a:hover {
    background-color: #f4f4f9;
}

.user-menu:hover .dropdown-content {
    display: block;
}</style>
      </nav>
    </header>
    <div class="section__container about__container" id="about">
      <h2 class="section__header">IMMORTALISEZ VOS MOMENTS</h2>
      <p class="section__description">
        Chez Visiora Photographie, nous capturons ces instants précieux qui ont une importance immense pour vous. Grâce à notre passion pour la photographie et notre souci du détail, nous transformons les moments ordinaires en souvenirs extraordinaires.
      </p>
      <p class="section__description">
        Que ce soit un événement marquant, un portrait spontané ou la beauté spectaculaire de la nature, nous cherchons à encapsuler l'essence de chaque instant, afin que vos souvenirs chers durent toute une vie.
      </p>
      <img src="photographie/logo2.png" alt="logo Visiora" />
    </div>

    <div class="section__container galerie__container">
      <h2 class="section__header">~ GALERIE ~</h2>
      <div class="galerie__grid">
        
        <div class="galerie__card">
          <img src="photographie/Mariage1.jpg" alt="galerie" />
          <div class="galerie__content">
            <button class="btn" data-categorie="Mariage">MARIAGE GALERIE</button>
          </div>
        </div>
        <div class="galerie__card">
          <img src="photographie/shooting1.jpg" alt="galerie" />
          <div class="galerie__content">
            <button class="btn" data-categorie="Shooting Mode et publicité">SHOOTING MODE ET PUB GALERIE</button>
          </div>
        </div>
        <div class="galerie__card">
          <img src="photographie/shooting5.jpg" alt="galerie" />
          <div class="galerie__content">
            <button class="btn" data-categorie="Shooting famille">SHOOTING FAMEILLE GALERIE</button>
          </div>
        </div>
        <div class="galerie__card">
          <img src="photographie/event1.jpg" alt="galerie" />
          <div class="galerie__content">
            <button class="btn" data-categorie="Événements">EVENEMENT GALERIE</button>
          </div>
        </div>
        <div class="galerie__card">
            <img src="photographie/bebe5.jpg" alt="galerie" />
            <div class="galerie__content">
              <button class="btn" data-categorie="Bébé">BÉBÉ GALERIE</button>
            </div>
        </div>
        <div class="galerie__card">
            <img src="photographie/animaux1.jpg" alt="galerie" />
            <div class="galerie__content">
              <button class="btn" data-categorie="Animaux">ANIMAUX GALERIE</button>
            </div>
        </div>
        <div class="galerie__card">
            <img src="photographie/balade4.jpg" alt="galerie" />
            <div class="galerie__content">
              <button class="btn" data-categorie="balade">BALADE GALERIE</button>
            </div>
        </div>
      </div>
    </div>

    <script>
  document.addEventListener("DOMContentLoaded", () => {
    // Select all the buttons
    const buttons = document.querySelectorAll(".galerie__content .btn");

    // Add event listener for each button
    buttons.forEach(button => {
      button.addEventListener("click", () => {
        // Get the category from the data-categorie attribute
        const categorie = button.getAttribute("data-categorie");

        // Display the category (or trigger other actions based on your requirement)
        alert(`You selected the category: ${categorie}`);

        // Example: You can filter the gallery by this category or perform other actions
        console.log(`Category: ${categorie}`);
      });
    });
  });
</script>

    <section class="service" id="service">
  <div class="section__container service__container">
    <h2 class="section__header">~ SERVICES ~</h2>
    <p class="section__description">
      Découvrez nos prestations et réservez un service selon vos besoins.
    </p>

    <!-- Liste des services -->
    <div class="service__grid">
      <?php foreach ($services as $service): ?>
        <div class="service__card">
          <h3><?= htmlspecialchars($service['nom']) ?></h3>
          <p><?= htmlspecialchars($service['description']) ?></p>
          <p><strong>Prix :</strong> <?= number_format($service['prix'], 2) ?> €</p>
        </div>
      <?php endforeach; ?>
      <button class="btn reserver-btn" data-service-id="<?php echo htmlspecialchars($service['idService']); ?>">
    Réserver
</button>
    </div>
  </div>

  <!-- Popup de réservation -->
  <div id="reservation-modal" class="modal">
    <div class="modal-content">
      <span class="close-btn">&times;</span>
      <h3>Choisir un type de prestation</h3>
      <form id="reservation-form" method="GET" action="calendrier.php">
        <label for="service-selection">Type de prestation :</label>
        <select id="service-selection" name="service_id" required>
          <option value="">Sélectionnez un service</option>
          <?php foreach ($services as $service): ?>
            <option value="<?= $service['idService'] ?>"><?= htmlspecialchars($service['nom']) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">Voir les créneaux</button>
      </form>
    </div>
  </div>
</section>

<script>
  // Ouvrir la popup
  document.querySelectorAll('.reserver-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('reservation-modal').style.display = 'block';
    });
  });

  // Fermer la popup
  document.querySelector('.close-btn').addEventListener('click', () => {
    document.getElementById('reservation-modal').style.display = 'none';
  });
</script>

    <!-- Section Boutique -->
    <section class="boutique" id="boutique">
  <div class="section__container boutique__container">
    <h2 class="section__header">~ BOUTIQUE ~</h2>
    <p class="section__description">
      Découvrez nos produits exclusifs en vente !
    </p>

    <div class="boutique__grid">
      <?php foreach ($produits as $produit): ?>
        <div class="boutique__card">
          <img src="<?= htmlspecialchars($produit['imageURL']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" />
          <div class="boutique__content">
            <h3><?= htmlspecialchars($produit['nom']) ?></h3>
            <p><?= htmlspecialchars($produit['description']) ?></p>

            <div class="boutique__price">
              <div class="prix-inline">
                <?php if ($produit['pourcentage'] > 0): ?>
                  <span class="prix-original"><?= number_format($produit['prix'], 2) ?> €</span>
                  <span class="pourcentage-reduction">-<?= $produit['pourcentage'] ?>%</span>
                <?php endif; ?>
              </div>
              <span class="prix-remise"><?= number_format($produit['prixApresRemise'], 2) ?> €</span>
            </div>

            <!-- Affichage de la moyenne des notes sous forme d'étoiles -->
            <div class="product-rating">
            <?php
              $moyenneNote = round($produit['moyenneNote']);  // Arrondir la moyenne à l'entier le plus proche
              for ($i = 1; $i <= 5; $i++) {
              echo ($i <= $moyenneNote) ? '<span class="star filled">★</span>' : '<span class="star">★</span>';
              }
              ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="boutique__cta">
      <a href="boutique.php" class="btn--black">Commander</a>
    </div>
    
  </div>
</section>


<!-- Section Avis -->
<?php
// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : null;
    $rating = isset($_POST['note']) ? (int)$_POST['note'] : null;
    $message = isset($_POST['avis']) ? htmlspecialchars($_POST['avis']) : null;

    // Vérifier si tous les champs sont remplis
    if ($name &&  $rating && $message) {
        // Insérer l'avis dans la base de données
        $stmt = $pdo->prepare("INSERT INTO avis (name, rating, message) VALUES (:name, :rating, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            echo "Merci pour votre avis !";
        } else {
            echo "Une erreur est survenue. Veuillez réessayer.";
        }
    } else {
        echo "Tous les champs doivent être remplis.";
    }
}
?>


<!-- Section Avis -->
<section class="avis" id="avis">
    <div class="section__container avis__container">
        <h2 class="section__header">~ Laissez votre Avis ~</h2>
        <p class="section__description">Dites-nous ce que vous pensez de notre service !</p>

        <!-- Formulaire d'avis -->
        <form action="accepte_avis.php" method="POST" id="avisForm">
            <div class="input__row">
                <input type="text" name="nom" placeholder="Votre Nom" required />
            </div>
            <div class="input__row">
                <textarea name="avis" rows="4" placeholder="Votre Avis" required></textarea>
            </div>

            <!-- Système d'étoiles pour la note -->
            <div class="rating">
                <input type="radio" name="note" id="star1" value="1">
                <label for="star1">&#9733;</label>

                <input type="radio" name="note" id="star2" value="2">
                <label for="star2">&#9733;</label>

                <input type="radio" name="note" id="star3" value="3">
                <label for="star3">&#9733;</label>

                <input type="radio" name="note" id="star4" value="4">
                <label for="star4">&#9733;</label>

                <input type="radio" name="note" id="star5" value="5">
                <label for="star5">&#9733;</label>
            </div>

            <button class="btn" type="submit">Publier</button>
        </form>

        <!-- Affichage des avis -->
        <div id="avisAffiches">
            <?php
            // Afficher les avis existants
            $stmt = $pdo->query("SELECT * FROM avis ORDER BY id DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rating = $row['rating'];
                echo '<div class="avis-item">';
                echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                
                // Affichage des étoiles
                echo '<div class="star-rating">';
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $rating) {
                        echo '<span class="star selected">&#9733;</span>';
                    } else {
                        echo '<span class="star">&#9733;</span>';
                    }
                }
                echo '</div>';
                // Affichage du message et du nom
                echo '<p>' . htmlspecialchars($row['message']) . '</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>
<style>
  /* Section Avis */
.avis {
    padding: 50px 20px;
    background-color: #f9f9f9;
    max-width: 1200px;
    margin: 0 auto;
}

.avis__container {
    text-align: center;
}

.input__row {
    margin-bottom: 15px;
}

.input__row input, .input__row textarea {
    padding: 10px;
    width: 80%;
    margin: 0 auto;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.rating label {
    font-size: 30px;
    cursor: pointer;
    color: #ddd; /* Couleur des étoiles non sélectionnées */
    transition: color 0.3s ease;
}

/* Lorsque l'étoile est sélectionnée ou survolée, elle devient dorée */
.rating input:checked ~ label,
.rating input:hover ~ label {
    color: #FFD700; /* Couleur dorée pour l'étoile survolée ou sélectionnée */
}



/* Cacher les boutons radio pour que seule l'apparence des labels soit visible */
.rating input {
    display: none;
}

/* Style pour le bouton de soumission */
.avis__container button {
    padding: 10px 20px;
    background-color: #333;
    color: white;
    border: none;
    font-size: 1rem;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.avis__container button:hover {
    background-color: #555;
}

/* Style pour les avis affichés */
.avis-item {
    background-color: #fff;
    margin: 10px 0;
    padding: 15px;
    border-radius: 5px;
    border: 1px solid #ddd;
    text-align: left;
}

.avis-item h3 {
    font-size: 1.1rem;
    color: #333;
}

.avis-item p {
    font-size: 1rem;
    color: #666;
}

/* Affichage des étoiles des avis */
.avis-item .star-rating {
    font-size: 20px;
    color: #FFD700; /* Couleur dorée pour les étoiles */
}

.avis-item .star-rating .star {
    color: #ddd; /* Couleur des étoiles non sélectionnées */
}

.avis-item .star-rating .star.selected {
    color: #FFD700; /* Couleur dorée pour la sélection */
}

</style>


<!-- Section Contact -->
<?php
// Initialisation de la variable pour stocker le message de confirmation ou d'erreur
$messageConfirmation = "";

// Vérification si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification que tous les champs requis sont remplis
    if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['message'])) {
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $message = htmlspecialchars(trim($_POST['message']));

        try {
            // Insertion des données dans la base de données (Assurez-vous que $pdo est bien initialisé)
            $stmt = $pdo->prepare("INSERT INTO messages_contact (name, email, message) VALUES (:name, :email, :message)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':message' => $message,
            ]);

            // Message de confirmation
            $messageConfirmation = "<p class='success-message'>Votre message a été envoyé avec succès.</p>";
        } catch (PDOException $e) {
            // Message d'erreur
            $messageConfirmation = "<p class='error-message'>Une erreur s'est produite lors de l'envoi de votre message. Veuillez réessayer.</p>";
        }
    } else {
        // Message d'erreur si les champs sont vides
        $messageConfirmation = "<p class='error-message'>Veuillez remplir tous les champs du formulaire.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous</title>
    <style>
        .success-message {
            color: green;
            margin-top: 1rem;
        }
        .error-message {
            color: red;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- Section Contact -->
    <section class="contact" id="contact">
        <div class="section__container contact__container">
            <!-- Formulaire Contact -->
            <div class="contact__form">
                <h2 class="section__header">Contactez-nous</h2>
                <p>Vous avez un projet en tête ou besoin d'un photographe pour concrétiser votre vision ? Nous serons ravis d'échanger avec vous !</p>
                <form action="" method="POST">
                    <div class="input__row">
                        <input type="text" name="name" placeholder="Votre Nom" required />
                        <input type="email" name="email" placeholder="Votre Email" required />
                    </div>
                    <textarea name="message" rows="5" placeholder="Votre Message" required></textarea>
                    <button class="btn" type="submit">Envoyer</button>
                </form>
                <!-- Message de confirmation ou d'erreur -->
                <div>
                    <?= $messageConfirmation ?>
                </div>
            </div>

            <!-- Informations du Studio -->
            <div class="contact__info">
                <h3>Informations du Studio</h3>
                <?php if (!empty($contacts)): ?>
                    <ul>
                        <li><strong>Adresse :</strong> <?= htmlspecialchars($contacts[0]['address'] ?? 'Non disponible') ?></li>
                        <li><strong>Téléphone :</strong> <?= htmlspecialchars($contacts[0]['phone'] ?? 'Non disponible') ?></li>
                        <li><strong>Email :</strong> <?= htmlspecialchars($contacts[0]['email'] ?? 'Non disponible') ?></li>
                        <li><strong>Horaires :</strong> <?= htmlspecialchars($contacts[0]['hours'] ?? 'Non disponibles') ?></li>
                    </ul>
                <?php else: ?>
                    <p>Aucune information de contact disponible.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</body>
</html>

<style>
  /* Section Contact */
  .contact {
      display: auto;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background-image: url('photographie/bébé8.jpg'); /* Image de couverture */
      background-size: cover;
      background-position: center;
      padding: 10px;
      height: 100vh; /* Prend toute la hauteur de l'écran */
      width: 100%; /* Prend toute la largeur de l'écran */
  }

  .contact__container {
      display: flex;
      justify-content: space-between;
      width: 80%;
      max-width: 1200px;
      color: white;
  }

  .contact__form,
  .contact__info {
      width: 48%; /* Chaque colonne occupe 48% de la largeur */
      padding: 30px;
      border-radius: 10px;
      background-color: rgba(0, 0, 0, 0.6); /* Fond semi-transparent */
  }

  .contact__form h2,
  .contact__info h3 {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: beige;
  }

  .contact__form p {
      font-size: 1rem;
      margin-bottom: 1rem;
  }

  .contact__form .input__row {
      display: flex;
      gap: 10px;
      margin-bottom: 15px;
  }

  .contact__form input, .contact__form textarea {
      width: 100%;
      padding: 1rem;
      margin-bottom: 1rem;
      border-radius: 5px;
      border: none;
  }

  .contact__form button {
      padding: 1rem;
      width: 100%;
      background-color: rgb(0, 0, 0);
      border: none;
      font-size: 1.2rem;
      cursor: pointer;
      color: white;
      border-radius: 5px;
  }

  .contact__form button:hover {
      background-color: rgb(28, 27, 27);
  }

  .contact__info ul {
      list-style: none;
      padding-left: 0;
      text-align: left; /* Alignement à gauche */
  }

  .contact__info ul li {
      margin-bottom: 1rem;
      line-height: 1.5; /* Espacement entre les lignes */
  }

  .contact__info li strong {
      color: rgb(247, 246, 246);
      border-radius: 10px;
  }
</style>





      <footer id="contact">
      <div class="section__container footer__container">
        <div class="footer__col">
          <img src="photographie/logo2.png" alt="logo Visiora" />
          <div class="footer__socials">
            <a href="#"><i class="ri-facebook-fill"></i></a>
            <a href="#"><i class="ri-instagram-line"></i></a>
            <a href="#"><i class="ri-twitter-fill"></i></a>
          </div>
        </div>
        <div class="footer__col">
          <ul class="footer__links">
            <li><a href="#home">ACCUEIL</a></li>
            <li><a href="#about">À PROPOS</a></li>
            <li><a href="#galerie">GALERIE</a></li>
            <li><a href="#service">SERVICES</a></li>
            <li><a href="#contact">CONTACT</a></li>
            <li><a href="#boutique">BOUTIQUE</a></li>
            <li><a href="#avis">Avis</a></li>
          </ul>
        </div>
        <div class="footer__col">
          <h4>RESTEZ CONNECTÉ</h4>
          <p>Rejoignez notre communauté et ne manquez aucun moment!</p>
        </div>
      </div>
      <div class="footer__bar">
        Copyright © 2025 Visiora Photographie. Tous droits réservés.
      </div>
    </footer>

    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="main.js"></script>
    <script src="services.php"></script>
  </body>
</html>
