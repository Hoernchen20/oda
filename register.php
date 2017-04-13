<?php
include_once 'inc/loginfunction.inc.php';

if(isset($_GET['register'])) {
  $error_msg = "";
 
  if (isset($_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['p'])) {
    //TODO add array for function 'AddUser'
    $UserData[] = array();
    
    // Bereinige und überprüfe die Daten
    $UserData['FirstName'] = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
    $UserData['LastName'] = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
    
    $UserData['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $UserData['email'] = filter_var($UserData['email'], FILTER_VALIDATE_EMAIL);
    if (!filter_var($UserData['email'], FILTER_VALIDATE_EMAIL)) {
      // keine gültige E-Mail
      $error_msg .= '<p class="error">The email address you entered is not valid</p>';
    }
 
    $UserData['password'] = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    $UserData['password'] = password_hash($UserData['password'], PASSWORD_DEFAULT);
    if (strlen($UserData['password']) < 60) {
      // Das gehashte Passwort sollte 128 Zeichen lang sein.
      // Wenn nicht, dann ist etwas sehr seltsames passiert
      $error_msg .= '<p class="error">Invalid password configuration.</p>';
    }
 
    // Benutzername und Passwort wurde auf der Benutzer-Seite schon überprüft.
    // Das sollte genügen, denn niemand hat einen Vorteil, wenn diese Regeln   
    // verletzt werden.
    
    
    //Check if email exist
    if (GetUserData($UserData['email']) != false) {
      // Ein Benutzer mit dieser E-Mail-Adresse existiert schon
      $error_msg .= '<p class="error">A user with this email address already exists.</p>';
    }
 
    // Noch zu tun: 
    // Wir müssen uns noch um den Fall kümmern, wo der Benutzer keine
    // Berechtigung für die Anmeldung hat indem wir überprüfen welche Art 
    // von Benutzer versucht diese Operation durchzuführen.
 
    if (empty($error_msg)) {
      // Trage den neuen Benutzer in die Datenbank ein
      if (AddUser($UserData) != false) {
        header('Location: login.php');
      } else {
        header('Location: ../error.php?err=Registration failure: INSERT');
      }
    }
  }
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Secure Login: Registration Form</title>
    <script type="text/JavaScript" src="js/sha512.js"></script> 
    <script type="text/JavaScript" src="js/forms.js"></script>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <!-- Anmeldeformular für die Ausgabe, wenn die POST-Variablen nicht gesetzt sind
    oder wenn das Anmelde-Skript einen Fehler verursacht hat. -->
    <h1>Register with us</h1>
    <?php
    if (!empty($error_msg)) {
      echo $error_msg;
    }
    ?>
    <ul>
      <li>Benutzernamen dürfen nur Ziffern, Groß- und Kleinbuchstaben und Unterstriche enthalten.</li>
      <li>E-Mail-Adressen müssen ein gültiges Format haben.</li>
      <li>Passwörter müssen mindest sechs Zeichen lang sein.</li>
      <li>Passwörter müssen enthalten
        <ul>
          <li>mindestens einen Großbuchstaben (A..Z)</li>
          <li>mindestens einen Kleinbuchstabenr (a..z)</li>
          <li>mindestens eine Ziffer (0..9)</li>
        </ul>
      </li>
      <li>Das Passwort und die Bestätigung müssen exakt übereinstimmen.</li>
    </ul>
    <form action="?register=1" method="post" name="registration_form">
      Vorname: <input type="text" name="firstname" id="firstname" /><br>
      Nachname: <input type="text" name="lastname" id="lastname" /><br>
      Email: <input type="text" name="email" id="email" /><br>
      Password: <input type="password" name="password" id="password"/><br>
      Confirm password: <input type="password" name="confirmpwd" id="confirmpwd" /><br>
      <input type="button" value="Register" onclick="return regformhash(this.form, this.form.firstname, this.form.lastname, this.form.email, this.form.password, this.form.confirmpwd);" /> 
    </form>
    <p>Return to the <a href="login.php">login page</a>.</p>
  </body>
</html>
