<?php
include_once "inc/loginfunction.inc.php";

sec_session_start();

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
  register();
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>OpenDocumentArchiv: Log In</title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <div class="login-page">
      <div class="form">
        <form class="register-form" action="?register=1" method="post" name="registration_form">
          <input type="text" name="firstname" id="firstname" placeholder="First Name">
          <input type="text" name="lastname" id="lastname" placeholder="Last Name">
          <input type="text" name="email" id="email" placeholder="email">
          <input type="password" name="password" id="password" placeholder="Password">
          <input type="password" name="confirmpwd" id="confirmpwd" placeholder="Confirm Password">
          <input type="button" value="Register" onclick="return regformhash(this.form, this.form.firstname, this.form.lastname, this.form.email, this.form.password, this.form.confirmpwd);">
          <p class="message">Already registered? <a href="#">Sign In</a></p>
        </form>
        <form class="login-form" action="?login=1" method="post" name="login_form">
          <?php
          if (isset($_GET['error'])) {
            echo '<p class="error">Error Logging In!</p>';
          }
          if(isset($_GET['successful'])) {
            echo "<h2>Registration successful</h2>";
          }
          ?>
          <input type="text" name="email" placeholder="email"/>
          <input type="password" name="password" id="password" placeholder="Password"/>
          <input type="submit" value="Login" />
          <p class="message">Not registered? <a href="#">Create an account</a></p>
        </form>
      </div>
    </div>
    <script type="text/JavaScript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/JavaScript" src="js/sha512.js"></script>
    <script type="text/JavaScript" src="js/forms.js"></script>
    <script type="text/JavaScript" src="js/index.js"></script>
  </body>
</html>
