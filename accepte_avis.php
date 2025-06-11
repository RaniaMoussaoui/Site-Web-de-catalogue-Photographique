<?php
// connexion à la base de données

$host = 'localhost';  
$dbname = 'visiora_db'; 
$username = 'root'; 
$password = '';  

try {
    // Créer une instance de PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configurer le mode d'erreur de PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}


// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données envoyées par le formulaire
    $name = htmlspecialchars($_POST['nom']);    // Nom de l'utilisateur
    $rating = (int)$_POST['note'];              // Note (étoiles)
    $message = htmlspecialchars($_POST['avis']); // Avis ou message

    // Vérifier si tous les champs sont remplis
    if (!empty($name) && !empty($rating) && !empty($message)) {
        try {
            // Préparer la requête SQL pour insérer les données dans la base de données
            $stmt = $pdo->prepare("INSERT INTO avis (name, rating, message) VALUES (:name, :rating, :message)");

            // Lier les paramètres à la requête préparée
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':rating', $rating);
            $stmt->bindParam(':message', $message);

            // Exécuter la requête
            if ($stmt->execute()) {
                // Rediriger l'utilisateur vers la page d'accueil avec un message de succès
                header("Location: acceuil.php?success=true");
                exit();
            } else {
                echo "Une erreur est survenue lors de l'enregistrement de votre avis. Veuillez réessayer.";
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de connexion ou d'exécution de la requête
            echo "Erreur de base de données : " . $e->getMessage();
        }
    } else {
        // Afficher un message d'erreur si les champs sont vides
        echo "Tous les champs doivent être remplis.";
    }
}
?>
