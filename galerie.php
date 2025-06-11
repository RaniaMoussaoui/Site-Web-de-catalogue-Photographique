<?php
// Connexion à la base de données
$host = "localhost";
$dbname = "visiora_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Initialisation de la variable $categorie
$categorie = !empty($_GET['categorie']) ? $_GET['categorie'] : null;

if ($categorie) {
    // Préparer et exécuter la requête pour récupérer les médias par catégorie
    $stmt = $pdo->prepare("SELECT typeMedia, mediaURL FROM galeries WHERE categorie = :categorie");
    $stmt->execute([':categorie' => $categorie]);
    $medias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $medias = []; // Aucune catégorie spécifiée
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie - <?= htmlspecialchars($categorie ?? "Toutes les catégories") ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400..900&family=Poppins:wght@100..900&display=swap">
</head>
<body>
    <div class="section__container">
        <!-- Titre de la galerie -->
        <h1 class="section__header">Galerie - <?= htmlspecialchars($categorie ?? "Toutes les catégories") ?></h1>

        <?php if (!empty($medias)): ?>
            <!-- Affichage des médias -->
            <div class="gallery">
                <?php foreach ($medias as $media): ?>
                    <?php
                    // Logique pour déterminer si un média (image ou vidéo) est vertical ou horizontal
                    $class = '';
                    if ($media['typeMedia'] === 'image') {
                        // Charger l'image pour obtenir ses dimensions
                        list($width, $height) = getimagesize($media['mediaURL']); // Cette fonction récupère les dimensions de l'image

                        // Si la hauteur est plus grande que la largeur, l'image est verticale
                        if ($height > $width) {
                            $class = 'vertical'; // Image verticale
                        } else {
                            $class = 'horizontal'; // Image horizontale
                        }
                    } elseif ($media['typeMedia'] === 'video') {
                        // Pour les vidéos, appliquer une classe vide pour l'instant, elle sera modifiée par JS
                        $class = 'video vertical'; // Par défaut, marquer la vidéo comme verticale
                    }
                    ?>
                    <div class="gallery-item <?= $class ?>">
                        <?php if ($media['typeMedia'] === 'image'): ?>
                            <img src="<?= htmlspecialchars($media['mediaURL']) ?>" alt="Image de <?= htmlspecialchars($categorie ?? "catégorie inconnue") ?>">
                        <?php elseif ($media['typeMedia'] === 'video'): ?>
                            <video controls class="video-media">
                                <source src="<?= htmlspecialchars($media['mediaURL']) ?>" type="video/mp4">
                                Votre navigateur ne supporte pas la lecture de vidéos.
                            </video>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Message si aucun média disponible -->
            <p class="message">Aucun média disponible pour la catégorie <?= htmlspecialchars($categorie ?? "Non spécifiée") ?>.</p>
        <?php endif; ?>

        <a href="acceuil.php" class="btn">Retour à l'accueil</a>
    </div>

    <script>
        // Appliquer une classe basée sur l'aspect ratio des vidéos
        document.addEventListener("DOMContentLoaded", function() {
            const videos = document.querySelectorAll('.video-media');

            videos.forEach(video => {
                video.onloadedmetadata = function() {
                    const width = video.videoWidth;
                    const height = video.videoHeight;

                    // Si la hauteur est plus grande que la largeur, la vidéo est verticale
                    if (height > width) {
                        video.classList.add('vertical');
                    } else {
                        video.classList.add('horizontal');
                    }
                }
            });
        });
    </script>
</body>

<style>
    :root {
        --text-dark: #000000;
        --text-light: #767268;
        --extra-light: #eef9f8;
        --white: #ffffff;
        --max-width: 1200px;
        --header-font: "Playfair Display", serif;
    }

/* Styles globaux */
body {
    font-family: "Poppins", sans-serif;
    background-color: #eef9f8;
    margin: 0;
    padding: 0;
    color: #000;
}

/* Section principale */
.section__container {
    max-width: 1200px;
    margin: auto;
    padding: 5rem 1rem;
}

.section__header {
    position: relative;
    width: fit-content;
    margin-inline: auto;
    padding-bottom: 0.5rem;
    font-size: 2.5rem;
    font-weight: 800;
    font-family: "Playfair Display", serif;
    color: #000;
    text-align: center;
}

.section__header::before {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    height: 2px;
    width: 3rem;
    background-color: #000;
}

/* Galerie */
.gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin-top: 2rem;
}

.gallery-item {
    flex: 1 1 calc(20% - 1rem); /* Trois cartes par ligne */
    max-width: calc(20% - 1rem);
    box-sizing: border-box;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    border-radius: 8px;
    height: 350px; /* Hauteur fixe pour toutes les cartes */
    display: flex; /* Pour aligner le contenu */
    align-items: center;
    justify-content: center;
}

/* Image dans les cartes */
.gallery-item img {
    width: 100%; /* S'adapte à la largeur */
    height: 100%; /* Remplit toute la hauteur */
    object-fit: cover; /* Ajuste l'image pour remplir sans distorsion */
    border-radius: 8px;
}

/* Vidéo dans les cartes */
.gallery-item video {
    width: 100%; /* S'adapte à la largeur */
    height: 100%; /* Remplit toute la hauteur */
    object-fit: cover; /* Ajuste la vidéo pour remplir sans distorsion */
    border-radius: 8px;
}


/* Effet au survol */
.gallery-item:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Bouton retour */
.btn {
    display: inline-block;
    margin-top: 2rem;
    padding: 1rem 2rem;
    font-size: 1rem;
    font-weight: 500;
    color: #fff;
    background-color: #000;
    border: none;
    border-radius: 5px;
    text-align: center;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #767268;
}
</style>

</html>
