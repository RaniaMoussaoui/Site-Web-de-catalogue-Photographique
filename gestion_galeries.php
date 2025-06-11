<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Traitement des actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter'])) {
        try {
            // Récupération des données du formulaire
            $categorie = $_POST['categorie'];
            $typeMedia = $_POST['typeMedia'];

            // Vérification que la catégorie n'est pas vide
            if (empty($categorie)) {
                $message = "Veuillez sélectionner une catégorie.";
            } else {
                // Gestion du fichier
                $mediaURL = '';
                if (isset($_FILES['mediaURL']) && $_FILES['mediaURL']['error'] === UPLOAD_ERR_OK) {
                    $mediaTmpPath = $_FILES['mediaURL']['tmp_name'];
                    $mediaName = uniqid() . '-' . $_FILES['mediaURL']['name'];
                    $mediaURL = 'uploads/' . $mediaName;

                    if (!move_uploaded_file($mediaTmpPath, $mediaURL)) {
                        throw new Exception("Erreur lors de l'upload du fichier.");
                    }
                }

                // Insertion dans la base de données
                $stmt = $pdo->prepare("INSERT INTO galeries (categorie, typeMedia, mediaURL) 
                                       VALUES (:categorie, :typeMedia, :mediaURL)");
                $stmt->execute([
                    ':categorie' => $categorie,
                    ':typeMedia' => $typeMedia,
                    ':mediaURL' => $mediaURL
                ]);

                $message = "Média ajouté avec succès!";
            }
        } catch (Exception $e) {
            $message = "Erreur lors de l'ajout du média: " . $e->getMessage();
        }
    }

    // Traitement de la suppression
    if (isset($_POST['supprimer'])) {
        try {
            $id = $_POST['id'];

            // Récupérer l'URL du média avant de le supprimer
            $stmt = $pdo->prepare("SELECT mediaURL FROM galeries WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $media = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($media) {
                // Supprimer le fichier physique
                if (file_exists($media['mediaURL'])) {
                    unlink($media['mediaURL']);
                }

                // Supprimer l'entrée de la base de données
                $stmt = $pdo->prepare("DELETE FROM galeries WHERE id = :id");
                $stmt->execute([':id' => $id]);

                $message = "Média supprimé avec succès!";
            } else {
                $message = "Média introuvable.";
            }
        } catch (Exception $e) {
            $message = "Erreur lors de la suppression du média: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de la Galerie</title>
    <style>
        /* Styles similaires à ceux que vous avez déjà définis */
    </style>
</head>
<body>
<div class="container">
    <h1>Gestion de la Galerie</h1>

    <!-- Message de confirmation ou d'erreur -->
    <?php if ($message): ?>
        <p style="text-align:center; font-size: 1.2rem; color: green;"><?= $message ?></p>
    <?php endif; ?>

    <!-- Formulaire d'ajout de média -->
    <div class="form-card">
        <form method="POST" enctype="multipart/form-data">
            <select name="categorie" required>
                <option value="">Sélectionnez une catégorie</option>
                <option value="Mariage">Mariage</option>
                <option value="Événements">Événements</option>
                <option value="Bébé">Bébé</option>
                <option value="Shooting famille">Shooting famille</option>
                <option value="Shooting Mode et publicité">Shooting Mode et publicité</option>
                <option value="Animaux">Animaux</option>
                <option value="balade">balade</option>
            </select>
            <select name="typeMedia" required>
                <option value="image">Image</option>
                <option value="video">Vidéo</option>
            </select>
            <input type="file" name="mediaURL" required>
            <button type="submit" name="ajouter" class="btn">Ajouter le média</button>
        </form>
    </div>

    <!-- Affichage de la galerie -->
    <div class="gallery">
        <h2>Galerie des Médias</h2>
        <div class="gallery-items">
            <?php
            $stmt = $pdo->query("SELECT * FROM galeries");
            $medias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($medias as $media) {
                echo '<div class="gallery-item">';
                echo '<p>Catégorie : ' . htmlspecialchars($media['categorie']) . '</p>';
                if ($media['typeMedia'] == 'image') {
                    echo '<img src="' . htmlspecialchars($media['mediaURL']) . '" alt="' . htmlspecialchars($media['categorie']) . '" class="gallery-image">';
                } elseif ($media['typeMedia'] == 'video') {
                    echo '<video controls class="gallery-video"><source src="' . htmlspecialchars($media['mediaURL']) . '" type="video/mp4"></video>';
                }
                echo '<form method="POST" style="margin-top: 1rem;">';
                echo '<input type="hidden" name="id" value="' . $media['id'] . '">';
                echo '<button type="submit" name="supprimer" class="btn" style="background-color: black; color: white;">Supprimer</button>';
                echo '</form>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>

<style>
        @import url("https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@400;500;700&display=swap");

        :root {
            --text-dark: #171717;
            --text-light: #525252;
            --extra-light: #a3a3a3;
            --white: #ffffff;
            --background: #f4f4f4;
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
            margin: 0 auto;
            background-color: var(--white);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        select, input[type="file"], button {
            width: 100%;
            margin-bottom: 1rem;
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
        }

        .btn:hover {
            background-color: var(--text-light);
        }

        .gallery {
            margin-top: 3rem;
            text-align: center;
        }

        .gallery-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .gallery-item {
            background-color: var(--white);
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .gallery-image, .gallery-video {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .gallery-item p {
            margin: 0.5rem 0;
            color: var(--text-light);
        }
    </style>