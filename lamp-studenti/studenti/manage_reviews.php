<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

require_once 'db_connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    if ($action === 'add') {
        // --- ADĂUGARE ---
        if(empty($input['nume']) || empty($input['comentariu']) || empty($input['nota'])) {
            echo json_encode(['success' => false, 'message' => 'Toate câmpurile sunt obligatorii!']);
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO recenzii (nume_utilizator, nota, comentariu) VALUES (:nume, :nota, :comentariu)");
        $stmt->execute([
            'nume' => htmlspecialchars($input['nume']), // Protecție XSS
            'nota' => $input['nota'],
            'comentariu' => htmlspecialchars($input['comentariu'])
        ]);
        echo json_encode(['success' => true, 'message' => 'Recenzie adăugată!']);

    } elseif ($action === 'delete') {
        // --- ȘTERGERE ---
        $stmt = $pdo->prepare("DELETE FROM recenzii WHERE id = :id");
        $stmt->execute(['id' => $input['id']]);
        echo json_encode(['success' => true, 'message' => 'Recenzie ștearsă!']);

    } else {
        echo json_encode(['success' => false, 'message' => 'Acțiune invalidă']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Eroare SQL: ' . $e->getMessage()]);
}
?>