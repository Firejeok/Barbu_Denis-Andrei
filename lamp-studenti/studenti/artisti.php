<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
// 1. Asigură-te că db_connect.php este inclus
require_once 'db_connect.php'; // Acum avem variabila $pdo

// Setează header-ul pentru a indica că răspunsul este în format JSON
header('Content-Type: application/json');

// Array-ul în care vom stoca toți artiștii
$artisti_data = [];

// 2. Interogarea bazei de date (SELECT)
$sql = "SELECT artist_id, nume_scena, gen_muzical, scena_alocata, orar_prezentare, descriere_scurta, url_poza, link_social_media 
        FROM program_artisti_frontend 
        ORDER BY nume_scena ASC"; 

// Folosim $pdo în loc de $conn
$stmt = $pdo->query($sql);

if ($stmt) {
    // 3. Parcurge fiecare rând și adaugă-l la array-ul PHP
    // PDO::FETCH_ASSOC este deja setat ca implicit în db_connect.php
    $artisti_data = $stmt->fetchAll();
}

// 4. Închide conexiunea - Nu este strict necesar la PDO, se face automat la sfârșitul scriptului
// Am eliminat $conn->close();

// 5. Converteste array-ul PHP în JSON și îl afișează (Acesta este API-ul!)
echo json_encode($artisti_data);
?>