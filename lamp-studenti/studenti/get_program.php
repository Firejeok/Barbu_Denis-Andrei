<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
require_once 'db_connect.php';
try {
    // Luăm programul ordonat după zi și oră
    $sql = "SELECT * FROM program_festival ORDER BY 
            CASE 
                WHEN ziua = 'Vineri' THEN 1 
                WHEN ziua = 'Sambata' THEN 2 
                WHEN ziua = 'Duminica' THEN 3 
            END, ora_inceput ASC";
            
    $stmt = $pdo->query($sql);
    $program = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $program]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>