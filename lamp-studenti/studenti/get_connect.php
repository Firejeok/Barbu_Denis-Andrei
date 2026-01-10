<?php
// === BLOC CORS OBLIGATORIU ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Tratăm cererea "preflight"
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db_connect.php'; 
header('Content-Type: application/json');

// Preluăm datele trimise din Frontend
$input = json_decode(file_get_contents('php://input'), true);
$response = [];

if (isset($input['email'])) {
    $email_cautat = $input['email'];

    try {
        // --- PASUL A: Căutăm datele personale ---
        $sql_user = "SELECT id, nume, email, telefon, data_inregistrare 
                     FROM users 
                     WHERE email = :email 
                     LIMIT 1";
        
        $stmt = $pdo->prepare($sql_user);
        $stmt->execute(['email' => $email_cautat]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $response['success'] = true;
            $response['user_info'] = $user;

            // --- PASUL B: Bilete (CU PROTECȚIE LA ERORI) ---
            $response['bilete'] = []; // Pornim cu lista goală

            try {
                // Încercăm să citim biletele. Dacă tabelul nu există, intră pe `catch`.
                $sql_bilete = "SELECT tip_bilet, data_eveniment, status_bilet, pdf_link 
                               FROM bilete 
                               WHERE user_id = :uid";
                
                $stmt_bilete = $pdo->prepare($sql_bilete);
                $stmt_bilete->execute(['uid' => $user['id']]);
                $bilete_gasite = $stmt_bilete->fetchAll(PDO::FETCH_ASSOC);
                
                if ($bilete_gasite) {
                    $response['bilete'] = $bilete_gasite;
                }
            } catch (Exception $e) {
                // Tabelul 'bilete' nu există sau altă eroare. 
                // Ignorăm eroarea și lăsăm lista goală.
            }

        } else {
            $response['success'] = false;
            $response['message'] = 'Utilizatorul nu a fost găsit.';
        }

    } catch (PDOException $e) {
        // Eroare critică la conexiune sau la tabelul 'users'
        $response['success'] = false;
        $response['message'] = 'Eroare SQL: ' . $e->getMessage();
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Nu s-a trimis niciun email.';
}

echo json_encode($response);
?>