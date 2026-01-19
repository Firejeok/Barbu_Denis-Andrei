<?php
// === CONFIGURĂRI DE COMUNICARE (CORS) ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Răspundem OK la verificările browserului
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// === CONECTAREA LA BAZA DE DATE ===
require_once 'db_connect.php'; 
header('Content-Type: application/json');

// Citim datele trimise de formular
$input = json_decode(file_get_contents('php://input'), true);

// Validare simplă
if (!isset($input['nume']) || !isset($input['gen'])) {
    echo json_encode(['success' => false, 'message' => 'Numele și Genul sunt obligatorii!']);
    exit();
}

try {
    // Inserăm în baza de date
    $sql = "INSERT INTO program_artisti_frontend 
            (nume_scena, gen_muzical, scena_alocata, orar_prezentare, descriere_scurta, url_poza, link_social_media) 
            VALUES (:nume, :gen, :scena, :ora, :descriere, :poza, :social)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        'nume' => $input['nume'],
        'gen' => $input['gen'],
        'scena' => $input['scena'] ?? 'Main Stage',
        'ora' => $input['ora'] ?? 'TBA',
        'descriere' => $input['descriere'] ?? '',
        'poza' => $input['poza'] ?? '', 
        'social' => '#'
    ]);

    echo json_encode(['success' => true, 'message' => 'Artist adăugat cu succes!']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Eroare SQL: ' . $e->getMessage()]);
}
?>