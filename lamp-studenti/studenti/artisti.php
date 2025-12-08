<?php
// 1. Asigură-te că db_connect.php este inclus
require_once 'db_connect.php'; 

// Setează header-ul pentru a indica că răspunsul este în format JSON
header('Content-Type: application/json');

// Array-ul în care vom stoca toți artiștii
$artisti_data = [];

// 2. Interogarea bazei de date (SELECT)
$sql = "SELECT artist_id, nume_scena, gen_muzical, scena_alocata, orar_prezentare, descriere_scurta, url_poza, link_social_media 
        FROM program_artisti_frontend 
        ORDER BY nume_scena ASC"; 
        
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // 3. Parcurge fiecare rând și adaugă-l la array-ul PHP
    while($row = $result->fetch_assoc()) {
        // Nu este neapărat nevoie de htmlspecialchars aici, dar îl putem folosi pentru siguranță
        $artisti_data[] = $row; 
    }
}

// 4. Închide conexiunea
$conn->close();

// 5. Converteste array-ul PHP în JSON și îl afișează (Acesta este API-ul!)
echo json_encode($artisti_data);
// NOTĂ: După ce rulezi acest fișier, în browser ar trebui să vezi doar un text structurat JSON.
?>