<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $email = isset($input['email']) ? trim($input['email']) : '';
    $password = isset($input['password']) ? $input['password'] : '';

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Te rog completează datele."]);
        exit();
    }

    try {
    // 1. ADĂUGĂM 'role' AICI ÎN LISTĂ:
    $stmt = $pdo->prepare("SELECT id, nume, prenume, email, parola, role FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['parola'])) {
        
        // 2. SALVĂM ROLUL ÎN SESIUNE:
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nume'];
        $_SESSION['role'] = $user['role']; // <--- IMPORTANT

        // 3. TRIMITEM TOT USERUL (CU ROL) ÎNAPOI LA JAVASCRIPT:
        echo json_encode([
            "success" => true, 
            "message" => "Autentificare reușită!",
            "user" => $user 
        ]);
    } else {
            echo json_encode(["success" => false, "message" => "Date incorecte."]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Eroare server: " . $e->getMessage()]);
    }
}
?>