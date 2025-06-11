<?php
session_start();

// Si l'utilisateur est déjà connecté, redirigez-le vers une autre page
if (isset($_SESSION['user_id'])) {
    header('Location: acceuil.php');
    exit;
}

// Gérer l'affichage du formulaire d'inscription ou de connexion
$formType = 'connexion'; // Le formulaire par défaut est la connexion

// Vérifier si le formulaire d'inscription a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Gérer l'inscription
    if (isset($_POST['inscription'])) {
        $formType = 'inscription';

        // Connexion à la base de données
        $pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les données du formulaire d'inscription
        $nom = htmlspecialchars($_POST['nom']);
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];

        // Hacher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Vérifier si l'email est déjà utilisé
        $stmt = $pdo->prepare('SELECT * FROM Client WHERE email = :email');
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            echo "Cet email est déjà utilisé.";
        } else {
            // Insérer l'utilisateur dans la base de données
            $stmt = $pdo->prepare('INSERT INTO client (nom, email, password) VALUES (:nom, :email, :password)');
            $stmt->execute([
                'nom' => $nom,
                'email' => $email,
                'password' => $hashedPassword
            ]);

            echo "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        }
    }

// Gérer la connexion
if (isset($_POST['connexion'])) {
    $formType = 'connexion';

    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=visiora_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les données du formulaire de connexion
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Vérifier si l'email existe dans la base de données
    $stmt = $pdo->prepare('SELECT * FROM Client WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Créer une session pour l'utilisateur
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];

        // Rediriger l'utilisateur vers la page boutique 
        header('Location: acceuil.php');
        exit;
    } else {
        echo "Email ou mot de passe incorrect.";
    }
}

}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion et Inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px;
            background-color: #000;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #000;
        }
        .switch-btn {
            text-align: center;
            margin-top: 15px;
        }
        .switch-btn a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
        .switch-btn a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2><?= $formType == 'connexion' ? 'Se connecter' : 'S\'inscrire' ?></h2>

        <!-- Formulaire de Connexion -->
        <div id="connexion-form" style="display: <?= $formType == 'connexion' ? 'block' : 'none' ?>;">
            <form action="" method="POST">
                <input type="email" name="email" placeholder="Votre email" required>
                <input type="password" name="password" placeholder="Votre mot de passe" required>
                <button type="submit" name="connexion">Se connecter</button>
            </form>

            <div class="switch-btn">
                <p>Vous n'avez pas de compte ? <a href="#" id="showInscription">S'inscrire</a></p>
            </div>
        </div>

        <!-- Formulaire d'Inscription -->
        <div id="inscription-form" style="display: <?= $formType == 'inscription' ? 'block' : 'none' ?>;">
            <form action="" method="POST">
                <input type="text" name="nom" placeholder="Votre nom" required>
                <input type="email" name="email" placeholder="Votre email" required>
                <input type="password" name="password" placeholder="Votre mot de passe" required>
                <button type="submit" name="inscription">S'inscrire</button>
            </form>

            <div class="switch-btn">
                <p>Vous avez déjà un compte ? <a href="#" id="showConnexion">Se connecter</a></p>
            </div>
        </div>
    </div>

    <script>
        // Bascule entre le formulaire de connexion et le formulaire d'inscription
        document.getElementById('showInscription').addEventListener('click', function(event) {
            event.preventDefault();
            // Masquer le formulaire de connexion et afficher celui d'inscription
            document.getElementById('connexion-form').style.display = 'none';
            document.getElementById('inscription-form').style.display = 'block';
        });

        document.getElementById('showConnexion').addEventListener('click', function(event) {
            event.preventDefault();
            // Masquer le formulaire d'inscription et afficher celui de connexion
            document.getElementById('inscription-form').style.display = 'none';
            document.getElementById('connexion-form').style.display = 'block';
        });
    </script>
</body>
</html>
