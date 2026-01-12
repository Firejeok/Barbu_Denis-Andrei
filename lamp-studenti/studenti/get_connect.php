<?php
// === BLOC CORS OBLIGATORIU ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
// =============================

require_once 'db_connect.php'; 
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$response = [];

if (isset($input['email'])) {
    $email_cautat = $input['email'];

    try {
        // 1. Căutăm ID-ul userului
        $sql_user = "SELECT id, nume, email, telefon FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql_user);
        $stmt->execute(['email' => $email_cautat]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $response['success'] = true;
            $response['user_info'] = $user;

            // 2. Căutăm biletele (Actualizat cu noile coloane)
            // Am scos 'pdf_link' dacă nu există în tabel, și am adăugat 'cantitate' și 'pret_total'
            $sql_bilete = "SELECT tip_bilet, cantitate, pret_total, data_eveniment, status_bilet 
                           FROM bilete 
                           WHERE user_id = :uid 
                           ORDER BY id DESC"; // Cele mai noi primele
            
            $stmt_bilete = $pdo->prepare($sql_bilete);
            $stmt_bilete->execute(['uid' => $user['id']]);
            $bilete = $stmt_bilete->fetchAll(PDO::FETCH_ASSOC);

            $response['bilete'] = $bilete;

        } else {
            $response['success'] = false;
            $response['message'] = 'Utilizatorul nu a fost găsit.';
        }

    } catch (PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'Eroare SQL: ' . $e->getMessage();
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Nu s-a trimis niciun email.';
}

echo json_encode($response);
?>