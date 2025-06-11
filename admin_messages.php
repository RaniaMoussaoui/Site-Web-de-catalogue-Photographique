<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer les messages
$stmt = $pdo->query("SELECT * FROM messages_contact ORDER BY sent_at DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages des visiteurs</title>
    <link rel="stylesheet" href="styles.css"> <!-- Inclure votre style ici -->
</head>
<body>
    <div class="admin-container">
        <h2>Messages des visiteurs</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= htmlspecialchars($message['id']) ?></td>
                        <td><?= htmlspecialchars($message['name']) ?></td>
                        <td><?= htmlspecialchars($message['email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($message['message'])) ?></td>
                        <td><?= htmlspecialchars($message['sent_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<style>
    /* Conteneur principal */
.admin-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 1rem;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
}

/* Titre principal */
.admin-container h2 {
    text-align: center;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    color: #333333;
}

/* Tableau */
.admin-container table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

/* En-têtes du tableau */
.admin-container th {
    background-color: #f4f4f4;
    color: #333333;
    text-align: left;
    padding: 12px 15px;
    font-weight: bold;
    border-bottom: 2px solid #dddddd;
}

/* Cellules du tableau */
.admin-container td {
    padding: 10px 15px;
    border-bottom: 1px solid #dddddd;
}

/* Ligne alternée */
.admin-container tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Ligne au survol */
.admin-container tr:hover {
    background-color: #f1f1f1;
}

/* Liens ou boutons d'action dans le tableau */
.admin-container a {
    color: #007bff;
    text-decoration: none;
}

.admin-container a:hover {
    text-decoration: underline;
}

/* Espacement autour des messages longs */
.admin-container td:last-child {
    word-wrap: break-word;
    max-width: 300px; /* Ajustez selon vos besoins */
    white-space: pre-wrap; /* Permet d'afficher les retours à la ligne */
}

/* Pour rendre le tableau responsive */
@media (max-width: 768px) {
    .admin-container table {
        font-size: 0.8rem;
    }

    .admin-container td,
    .admin-container th {
        padding: 8px;
    }
}

</style>