<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
session_start();
session_unset();
session_destroy();

// Îl trimitem înapoi la pagina principală sau la login
header("Location: Login.html");
exit();
?>