<?php
include_once 'inc/loginfunction.inc.php';

sec_session_start();
 
if (login_check() == true) {
    $logged = 'in';
} else {
    $logged = 'out';
}

if(isset($_GET['login'])) {
  if (isset($_POST['email'], $_POST['password'])) {
    $email = mb_strtolower($_POST['email'], 'UTF-8');
    $password = $_POST['password']; // Das gehashte Passwort.
 
    if (login($email, $password) == true) {
      // Login erfolgreich 
      header('Location: index.php');
    } else {
      // Login fehlgeschlagen 
      header('Location: login.php?error=1');
    }
  } else {
    // Die korrekten POST-Variablen wurden nicht zu dieser Seite geschickt. 
    echo 'Invalid Request';
  }
}

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
    $UserData['email'] = mb_strtolower($UserData['email'], 'UTF-8');
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
      <title>Secure Login: Log In</title>
      <link rel="stylesheet" href="styles.css">
    </head>
    <body>
      <div class="login-page">
        <div class="form">
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error">Error Logging In!</p>';
        }
        ?>
        <form class="register-form" action="?register=1" method="post" name="registration_form">
          <input type="text" name="firstname" id="firstname" placeholder="Vorname">
          <input type="text" name="lastname" id="lastname" placeholder="Nachname">
          <input type="text" name="email" id="email" placeholder="email">
          <input type="password" name="password" id="password" placeholder="Password">
          <input type="password" name="confirmpwd" id="confirmpwd" placeholder="Confirm Password">
          <input type="button" value="Register" onclick="return regformhash(this.form, this.form.firstname, this.form.lastname, this.form.email, this.form.password, this.form.confirmpwd);">
          <p class="message">Already registered? <a href="#">Sign In</a></p>
        </form>
        <form class="login-form" action="?login=1" method="post" name="login_form">                      
            <input type="text" name="email" placeholder="email"/>
            <input type="password" name="password" id="password" placeholder="password"/>
            <input type="submit" value="Login" />
            <p class="message">Not registered? <a href="#">Create an account</a></p>
        </form>
      </div>
    </div>
    <!--
        <p>If you don't have a login, please <a href="register.php">register</a></p>
        <p>If you are done, please <a href="inc/logout.php">log out</a>.</p>
        <p>You are currently logged <?php echo $logged ?>.</p>
    -->
      <script type="text/JavaScript" src="js/jquery-3.2.1.min.js"></script>
      <script type="text/JavaScript" src="js/sha512.js"></script>
      <script type="text/JavaScript" src="js/forms.js"></script>
      <script type="text/JavaScript" src="js/index.js"></script>
    </body>
</html>
