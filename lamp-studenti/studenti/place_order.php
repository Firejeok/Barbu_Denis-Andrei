<?php
// Configurare CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db_connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['email']) || !isset($input['items'])) {
    echo json_encode(['success' => false, 'message' => 'Date incomplete.']);
    exit();
}

$email = $input['email'];
$items = $input['items'];

try {
    // 1. Găsim ID-ul utilizatorului pe baza email-ului
    $stmtUser = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmtUser->execute(['email' => $email]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Utilizator necunoscut.']);
        exit();
    }

    $userId = $user['id'];

    // 2. Introducem fiecare bilet în tabelul 'bilete'
    $sqlInsert = "INSERT INTO bilete (user_id, tip_bilet, cantitate, pret_total, status_bilet, data_eveniment) 
                  VALUES (:uid, :tip, :qty, :pret, 'Valid', '15-17 August 2025')";
    
    $stmtInsert = $pdo->prepare($sqlInsert);

    foreach ($items as $item) {
        $stmtInsert->execute([
            'uid' => $userId,
            'tip' => $item['type'],
            'qty' => $item['quantity'],
            'pret' => $item['total']
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Comanda a fost salvată!']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Eroare SQL: ' . $e->getMessage()]);
}
?>