<?php
include_once 'functions.inc.php';
 
sec_session_start(); // Unsere selbstgemachte sichere Funktion um eine PHP-Sitzung zu starten.
 
if (isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password']; // Das gehashte Passwort.
 
    if (login($email, $password) == true) {
        // Login erfolgreich 
        header('Location: ../index.php');
    } else {
        // Login fehlgeschlagen 
        header('Location: ../login.php?error=1');
    }
} else {
    // Die korrekten POST-Variablen wurden nicht zu dieser Seite geschickt. 
    echo 'Invalid Request';
}
?>
