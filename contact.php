<?php
// Connexion à la base de données
$host = 'localhost';
$db = 'visiora_db';
$user = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Sauvegarder les informations de contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['address', 'phone', 'email', 'hours'];
    $updateData = [];

    foreach ($fields as $field) {
        if (!empty($_POST[$field])) {
            $updateData[$field] = $_POST[$field];
        }
    }

    if (!empty($updateData)) {
        $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($updateData)));
        $stmt = $pdo->prepare("UPDATE contacts SET $setClause WHERE id = 1");
        $stmt->execute($updateData);
        $success = "Les informations de contact ont été mises à jour avec succès.";
    } else {
        $error = "Aucune modification apportée.";
    }
}

// Charger les données existantes
$stmt = $pdo->query("SELECT * FROM contacts WHERE id = 1");
$currentContact = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Contacts</title>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
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

        input[type="text"], input[type="email"], input[type="tel"], button {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid var(--extra-light);
            border-radius: 5px;
        }

        button {
            background-color: var(--text-dark);
            color: var(--white);
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: var(--text-light);
        }

        .message {
            text-align: center;
            margin-bottom: 1rem;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Gestion des Informations de Contact</h1>

    <!-- Message de succès ou d'erreur -->
    <?php if (isset($success)): ?>
        <p class="message success"><?= htmlspecialchars($success) ?></p>
    <?php elseif (isset($error)): ?>
        <p class="message error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <label for="address">Adresse</label>
            <input type="text" id="address" name="address" value="<?= htmlspecialchars($currentContact['address'] ?? '') ?>" placeholder="Entrez une nouvelle adresse">

            <label for="phone">Téléphone</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($currentContact['phone'] ?? '') ?>" placeholder="Entrez un nouveau numéro de téléphone">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($currentContact['email'] ?? '') ?>" placeholder="Entrez une nouvelle adresse email">

            <label for="hours">Horaires</label>
            <input type="text" id="hours" name="hours" value="<?= htmlspecialchars($currentContact['hours'] ?? '') ?>" placeholder="Entrez de nouveaux horaires">

            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</div>
</body>
</html>
