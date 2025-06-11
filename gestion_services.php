<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Initialisation des variables
$message = '';
$serviceToEdit = null;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouterService'])) {
        try {
            // Validation des données
            if (empty($_POST['nom']) || empty($_POST['description']) || empty($_POST['prix']) || empty($_POST['duree'])) {
                throw new Exception('Tous les champs doivent être remplis.');
            }

            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $prix = $_POST['prix'];
            $duree = $_POST['duree'];  // Récupérer la durée

            // Validation du prix et de la durée
            if (!is_numeric($prix) || !is_numeric($duree)) {
                throw new Exception('Le prix et la durée doivent être des nombres valides.');
            }

            // Insertion dans la table services
            $stmt = $pdo->prepare("INSERT INTO services (nom, description, prix, duree) VALUES (:nom, :description, :prix, :duree)");
            $stmt->execute([
                ':nom' => $nom,
                ':description' => $description,
                ':prix' => $prix,
                ':duree' => $duree  // Insérer la durée
            ]);

            $message = "Service ajouté avec succès!";
        } catch (Exception $e) {
            $message = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }

    if (isset($_POST['modifierService'])) {
        try {
            // Modification du service
            $idService = $_POST['idService'];
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $prix = $_POST['prix'];
            $duree = $_POST['duree'];  // Récupérer la durée

            // Mise à jour
            $stmt = $pdo->prepare("UPDATE services SET nom = :nom, description = :description, prix = :prix, duree = :duree WHERE idService = :idService");
            $stmt->execute([
                ':nom' => $nom,
                ':description' => $description,
                ':prix' => $prix,
                ':duree' => $duree,  // Mettre à jour la durée
                ':idService' => $idService
            ]);

            $message = "Service modifié avec succès!";

            // Redirection pour revenir au formulaire d'ajout
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } catch (Exception $e) {
            $message = "Erreur lors de la modification : " . $e->getMessage();
        }
    }
}

// Suppression d'un service
if (isset($_GET['supprimer'])) {
    $idService = $_GET['supprimer'];
    try {
        $stmt = $pdo->prepare("DELETE FROM services WHERE idService = :idService");
        $stmt->execute([':idService' => $idService]);
        $message = "Service supprimé avec succès!";
    } catch (Exception $e) {
        $message = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Récupération d'un service pour modification
if (isset($_GET['modifier'])) {
    $idService = $_GET['modifier'];
    $stmt = $pdo->prepare("SELECT * FROM services WHERE idService = :idService");
    $stmt->execute([':idService' => $idService]);
    $serviceToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupération des services
$stmt = $pdo->query("SELECT * FROM services");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Services</title>
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
            margin: 0 auto 2rem auto;
            background-color: var(--white);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        input, textarea {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid var(--extra-light);
            border-radius: 5px;
            font-size: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            font-size: 1rem;
            color: var(--white);
            background-color: var(--text-dark);
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .btn:hover {
            background-color: var(--text-light);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        table th, table td {
            border: 1px solid var(--extra-light);
            padding: 1rem;
            text-align: left;
        }

        table th {
            background-color: var(--background);
        }

        .btn-modifier {
            background-color: var(--text-dark);
        }

        .btn-supprimer {
            background-color: var(--text-dark);
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Gestion des Services</h1>

    <!-- Message de feedback -->
    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <input type="hidden" name="idService" value="<?= $serviceToEdit['idService'] ?? '' ?>">
            <input type="text" name="nom" placeholder="Nom du service" value="<?= htmlspecialchars($serviceToEdit['nom'] ?? '') ?>" required>
            <textarea name="description" placeholder="Description" required><?= htmlspecialchars($serviceToEdit['description'] ?? '') ?></textarea>
            <input type="number" name="prix" placeholder="Prix" step="0.01" value="<?= htmlspecialchars($serviceToEdit['prix'] ?? '') ?>" required>
            
            <!-- Nouveau champ pour la durée -->
            <input type="number" name="duree" placeholder="Durée en minutes" value="<?= htmlspecialchars($serviceToEdit['duree'] ?? '') ?>" required>

            <button type="submit" name="<?= $serviceToEdit ? 'modifierService' : 'ajouterService' ?>" class="btn">
                <?= $serviceToEdit ? 'Mettre à jour' : 'Ajouter le service' ?>
            </button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Durée (min)</th>  <!-- Nouvelle colonne -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
                <tr>
                    <td><?= htmlspecialchars($service['nom']) ?></td>
                    <td><?= htmlspecialchars($service['description']) ?></td>
                    <td><?= number_format($service['prix'], 2) ?> €</td>
                    <td><?= htmlspecialchars($service['duree']) ?> min</td> <!-- Afficher la durée -->
                    <td>
                        <a href="?modifier=<?= $service['idService'] ?>" class="btn btn-modifier">Modifier</a>
                        <a href="?supprimer=<?= $service['idService'] ?>" class="btn btn-supprimer" onclick="return confirm('Voulez-vous supprimer ce service ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
