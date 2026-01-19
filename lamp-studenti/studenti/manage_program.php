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
        $stmt = $pdo->prepare("INSERT INTO program_festival (ziua, ora_inceput, ora_sfarsit, nume_act, scena) VALUES (:zi, :start, :end, :nume, :scena)");
        $stmt->execute([
            'zi' => $input['ziua'],
            'start' => $input['ora_inceput'],
            'end' => $input['ora_sfarsit'],
            'nume' => $input['nume_act'],
            'scena' => $input['scena']
        ]);
        echo json_encode(['success' => true, 'message' => 'Adăugat cu succes!']);

    } elseif ($action === 'delete') {
        // --- ȘTERGERE ---
        $stmt = $pdo->prepare("DELETE FROM program_festival WHERE id = :id");
        $stmt->execute(['id' => $input['id']]);
        echo json_encode(['success' => true, 'message' => 'Șters cu succes!']);

    } else {
        echo json_encode(['success' => false, 'message' => 'Acțiune invalidă']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Eroare SQL: ' . $e->getMessage()]);
}
?>