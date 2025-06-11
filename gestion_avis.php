<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Traitement de la suppression
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
    try {
        $id = $_POST['id'];
        // Suppression de l'avis de la base de données
        $stmt = $pdo->prepare("DELETE FROM avis WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $message = "Avis supprimé avec succès!";
    } catch (Exception $e) {
        $message = "Erreur lors de la suppression de l'avis: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Avis</title>
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

        .message {
            text-align: center;
            font-size: 1.2rem;
            color: green;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        table th, table td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: var(--text-dark);
            color: var(--white);
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            font-size: 1rem;
            color: var(--white);
            background-color: var(--text-dark);
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: var(--text-light);
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Gestion des Avis</h1>

    <!-- Message de confirmation -->
    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Tableau des avis -->
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Avis</th>
            <th>Notation</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Récupération des avis
        $stmt = $pdo->query("SELECT * FROM avis ORDER BY id DESC");
        $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($avis as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['message']) ?></td>
                <td><?= htmlspecialchars($row['rating']) ?> / 5</td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="supprimer" class="btn">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
