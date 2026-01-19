<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

require_once 'db_connect.php';
header('Content-Type: application/json');

// Citim datele JSON trimise de formular
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['nume']) || !isset($input['email']) || !isset($input['mesaj'])) {
    echo json_encode(['success' => false, 'message' => 'Toate câmpurile sunt obligatorii!']);
    exit();
}

try {
    // Inserăm mesajul în baza de date
    $sql = "INSERT INTO mesaje_contact (nume, email, mesaj) VALUES (:nume, :email, :mesaj)";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        'nume' => htmlspecialchars($input['nume']),
        'email' => htmlspecialchars($input['email']),
        'mesaj' => htmlspecialchars($input['mesaj'])
    ]);

    echo json_encode(['success' => true, 'message' => 'Mesaj trimis cu succes!']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Eroare SQL: ' . $e->getMessage()]);
}
?>