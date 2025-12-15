<?php
// 1. Configurare CORS și JSON (La fel ca la register)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Gestionăm cererile preliminare
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Pornim sesiunea (OBLIGATORIU pentru login)
session_start();

require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Preluăm datele (compatibil cu JSON sau FormData)
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Dacă nu e JSON, încercăm $_POST standard
    $email = isset($input['email']) ? trim($input['email']) : (isset($_POST['email']) ? trim($_POST['email']) : '');
    $password = isset($input['password']) ? $input['password'] : (isset($_POST['password']) ? $_POST['password'] : '');

    // Validare simplă
    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Te rog completează email-ul și parola."]);
        exit();
    }

    try {
        // 3. Căutăm utilizatorul în baza de date
        // Selectăm și parola pentru verificare, și ID/Nume pentru sesiune
        $stmt = $pdo->prepare("SELECT id, nume, prenume, email, parola FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 4. Verificăm parola HASH-uită
        if ($user && password_verify($password, $user['parola'])) {
            
            // SUCCES: Salvăm datele în sesiune
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nume'];
            $_SESSION['user_email'] = $user['email'];

            echo json_encode([
                "success" => true, 
                "message" => "Autentificare reușită! Se redirecționează..."
            ]);
        } else {
            // Eșec
            echo json_encode(["success" => false, "message" => "Email sau parolă incorectă."]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Eroare server: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Metodă invalidă."]);
}
?>