<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once 'db_connect.php';

try {
    // Luăm recenziile, cele mai noi primele
    $sql = "SELECT * FROM recenzii ORDER BY data_postarii DESC";
    $stmt = $pdo->query($sql);
    $recenzii = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $recenzii]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>