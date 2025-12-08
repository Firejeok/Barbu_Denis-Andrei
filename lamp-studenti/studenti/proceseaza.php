<?php
// Inițializăm un array pentru a stoca erorile de validare
$errors = [];

// Verificăm dacă formularul a fost trimis
if (isset($_POST['submit'])) {
    
    // Funcție de curățare (securizare de bază) a datelor
    function curata_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // 1. Colectarea și curățarea datelor
    $nume = curata_input($_POST['nume'] ?? '');
    $email = curata_input($_POST['email'] ?? '');
    $mesaj = curata_input($_POST['mesaj'] ?? '');

    // 2. Validarea Câmpului Nume
    if (empty($nume)) {
        $errors['nume'] = "Numele este obligatoriu.";
    } elseif (strlen($nume) < 3) {
        $errors['nume'] = "Numele trebuie să conțină minim 3 caractere.";
    }

    // 3. Validarea Câmpului Email
    if (empty($email)) {
        $errors['email'] = "Adresa de email este obligatorie.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Formatul adresei de email este invalid.";
    }

    // 4. Validarea Câmpului Mesaj
    if (empty($mesaj)) {
        $errors['mesaj'] = "Mesajul este obligatoriu.";
    } elseif (strlen($mesaj) < 10) {
        $errors['mesaj'] = "Mesajul trebuie să conțină minim 10 caractere.";
    }

    // 5. Procesarea finală
    if (empty($errors)) {
        // Dacă nu există erori, procesăm datele (ex: le salvăm în DB, trimitem email, etc.)
        
        // Simulare de procesare reușită:
        $success_message = "✅ Mesajul a fost trimis cu succes! Vă mulțumim, $nume.";
        $data_details = "Detalii trimise: Nume: $nume, Email: $email, Mesaj: $mesaj";
        echo $success_message;
        echo $data_details;
        
        // Aici ar putea fi logica de trimitere email:
        // mail($to, $subject, $body, $headers); 

    } else {
        // Există erori de validare
        $error_message = "❌ Eroare la trimitere: Vă rugăm să corectați câmpurile marcate mai jos.";
        echo $error_message;
        // Verificarea și afișarea mesajului de eroare pentru Nume
if (isset($errors['nume'])) {
    echo '<p style="color: red;">' . $errors['nume'] . '</p>';
}

// Verificarea și afișarea mesajului de eroare pentru Email
if (isset($errors['email'])) {
    echo '<p style="color: red;">' . $errors['email'] . '</p>';
}

// Verificarea și afișarea mesajului de eroare pentru Mesaj
if (isset($errors['mesaj'])) {
    echo '<p style="color: red;">' . $errors['mesaj'] . '</p>';
}
    }
}
?>
