<?php
header("Access-Control-Allow-Origin: *");
require_once 'db_connect.php';
header('Content-Type: application/json');

try {
    // Luăm donațiile + Numele și Emailul donatorului
    $sql = "SELECT donatii.id, donatii.suma, donatii.mesaj, donatii.data_donatie, users.nume, users.prenume, users.email 
            FROM donatii 
            JOIN users ON donatii.user_id = users.id 
            ORDER BY donatii.data_donatie DESC";
            
    $stmt = $pdo->query($sql);
    $donatii = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $donatii]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>