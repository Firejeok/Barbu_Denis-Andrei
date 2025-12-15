<?php
// 1. Configurări CORS (Permite accesul de pe Live Server)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// 2. IMPORTANT: Gestionarea cererii "Preflight" (OPTIONS)
// Browserul trimite uneori o cerere de test înainte de POST. Trebuie să îi răspundem cu OK.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db_connect.php';

// 3. Verificăm dacă primim date prin POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Verificăm dacă datele vin ca JSON (cazul fetch) sau ca Form Data clasic
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

    if (strpos($contentType, 'application/json') !== false) {
        // Dacă JS trimite JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $nume = trim($input['nume'] ?? '');
        $prenume = trim($input['prenume'] ?? '');
        $email = trim($input['email'] ?? '');
        $telefon = trim($input['telefon'] ?? '');
        $password = $input['password'] ?? '';
        $confirm_password = $input['confirm_password'] ?? '';
    } else {
        // Dacă JS trimite FormData (varianta din exemplul anterior)
        $nume = trim($_POST['nume'] ?? '');
        $prenume = trim($_POST['prenume'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefon = trim($_POST['telefon'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
    }

    // Validări
    if (empty($nume) || empty($prenume) || empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Toate câmpurile sunt obligatorii."]);
        exit();
    }

    if ($password !== $confirm_password) {
        echo json_encode(["success" => false, "message" => "Parolele nu coincid."]);
        exit();
    }

    try {
        // Verificare email existent
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => false, "message" => "Acest email este deja folosit."]);
            exit();
        }

        // Kriptare parolă
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Inserare în DB
        $sql = "INSERT INTO users (nume, prenume, email, telefon, parola) 
                VALUES (:nume, :prenume, :email, :telefon, :parola)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'nume' => $nume,
            'prenume' => $prenume,
            'email' => $email,
            'telefon' => $telefon,
            'parola' => $hashed_password
        ]);

        if ($result) {
            echo json_encode(["success" => true, "message" => "Cont creat cu succes!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Eroare la baza de date."]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Eroare server: " . $e->getMessage()]);
    }
} else {
    // AICI ajungi dacă deschizi fișierul direct în browser
    echo json_encode(["success" => false, "message" => "Metodă invalidă. Folosește formularul de înregistrare."]);
}
?>