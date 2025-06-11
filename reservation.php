<?php
header('Content-Type: application/json');

// Connexion à la base de données
$host = 'localhost';  
$dbname = 'visiora_db';  
$username = 'root';  
$password = '';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données.']);
    exit();
}

// Récupérer les données envoyées via POST
$data = json_decode(file_get_contents("php://input"), true);

$serviceId = $data['serviceId'];
$date = $data['date'];
$clientName = $data['clientName'];
$clientEmail = $data['clientEmail'];

// Vérifier si les données sont valides
if (empty($serviceId) || empty($date) || empty($clientName) || empty($clientEmail)) {
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit();
}

// Préparer la requête SQL pour insérer la réservation
$sql = "INSERT INTO reservations (id_service, date_reservation, client_nom, client_email) 
        VALUES (:serviceId, :date, :clientName, :clientEmail)";
$stmt = $pdo->prepare($sql);

try {
    // Exécuter la requête
    $stmt->execute([
        ':serviceId' => $serviceId,
        ':date' => $date,
        ':clientName' => $clientName,
        ':clientEmail' => $clientEmail
    ]);

    // Retourner une réponse JSON de succès
    echo json_encode(['success' => true, 'message' => 'Réservation confirmée.']);
} catch (PDOException $e) {
    // Retourner une réponse JSON d'erreur
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la réservation.']);
}
?>
