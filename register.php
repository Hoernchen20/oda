<?php
include_once 'inc/loginfunction.inc.php';

if(isset($_GET['register'])) {
  $error_msg = "";
 
  if (isset($_POST['username'], $_POST['email'], $_POST['p'])) {
    //TODO add array for function 'AddUser'
    
    // Bereinige und überprüfe die Daten
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      // keine gültige E-Mail
      $error_msg .= '<p class="error">The email address you entered is not valid</p>';
    }
 
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    $password = password_hash($password, PASSWORD_DEFAULT);
    if (strlen($password) < 60) {
      // Das gehashte Passwort sollte 128 Zeichen lang sein.
      // Wenn nicht, dann ist etwas sehr seltsames passiert
      $error_msg .= '<p class="error">Invalid password configuration.</p>';
    }
 
    // Benutzername und Passwort wurde auf der Benutzer-Seite schon überprüft.
    // Das sollte genügen, denn niemand hat einen Vorteil, wenn diese Regeln   
    // verletzt werden.
    
    
    //Check if email exist
    if (GetUserData($email) != NULL) {
      // Ein Benutzer mit dieser E-Mail-Adresse existiert schon
      $error_msg .= '<p class="error">A user with this email address already exists.</p>';
    }
 
    // Noch zu tun: 
    // Wir müssen uns noch um den Fall kümmern, wo der Benutzer keine
    // Berechtigung für die Anmeldung hat indem wir überprüfen welche Art 
    // von Benutzer versucht diese Operation durchzuführen.
 
    if (empty($error_msg)) {
      // Trage den neuen Benutzer in die Datenbank ein
      AddUser($UserData);
      
      
      if ($insert_stmt = $mysqli->prepare("INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)")) {
        $insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt);
        // Führe die vorbereitete Anfrage aus.
        if (! $insert_stmt->execute()) {
          header('Location: ../error.php?err=Registration failure: INSERT');
        }
      }
      header('Location: ./register_success.php');
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
      Username: <input type='text' name='username' id='username' /><br>
      Email: <input type="text" name="email" id="email" /><br>
      Password: <input type="password" name="password" id="password"/><br>
      Confirm password: <input type="password" name="confirmpwd" id="confirmpwd" /><br>
      <input type="button" value="Register" onclick="return regformhash(this.form, this.form.username, this.form.email, this.form.password, this.form.confirmpwd);" /> 
    </form>
    <p>Return to the <a href="login.php">login page</a>.</p>
  </body>
</html>
