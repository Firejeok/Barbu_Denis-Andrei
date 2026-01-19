<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

require_once 'db_connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['email']) || !isset($input['suma'])) {
    echo json_encode(['success' => false, 'message' => 'Date incomplete!']);
    exit();
}

try {
    // 1. Găsim ID-ul userului pe baza emailului
    $stmtUser = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmtUser->execute(['email' => $input['email']]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Utilizator negăsit.']);
        exit();
    }

    // 2. Inserăm donația
    $sql = "INSERT INTO donatii (user_id, suma, mesaj) VALUES (:uid, :suma, :mesaj)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'uid' => $user['id'],
        'suma' => $input['suma'],
        'mesaj' => $input['mesaj'] ?? ''
    ]);

    echo json_encode(['success' => true, 'message' => 'Mulțumim pentru donație!']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Eroare SQL: ' . $e->getMessage()]);
}
?>