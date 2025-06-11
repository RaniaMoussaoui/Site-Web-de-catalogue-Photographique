<?php
header("Content-Type: application/json");
// Connexion à la base de données
$host = 'localhost';
$db = 'visiora_db';
$user = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour récupérer les services
    $stmt = $pdo->query("SELECT idService, nom, description, prix FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourner les données en JSON
    echo json_encode($services);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
