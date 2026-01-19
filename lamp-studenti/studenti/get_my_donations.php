<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

require_once 'db_connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email lipsă!']);
    exit();
}

try {
    // 1. Găsim ID-ul userului pe baza emailului
    $stmtUser = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmtUser->execute(['email' => $input['email']]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User inexistent']);
        exit();
    }

    // 2. Luăm donațiile userului
    $sql = "SELECT suma, mesaj, data_donatie FROM donatii 
            WHERE user_id = :uid 
            ORDER BY data_donatie DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['uid' => $user['id']]);
    $donatii = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'donatii' => $donatii]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>