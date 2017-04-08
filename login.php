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
    $email = $_POST['email'];
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

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Secure Login: Log In</title>
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error">Error Logging In!</p>';
        }
        ?> 
        <form action="?login=1" method="post" name="login_form">                      
            Email: <input type="text" name="email" />
            Password: <input type="password" 
                             name="password" 
                             id="password"/>
            <input type="submit" 
                   value="Login" /> 
        </form>
        <p>If you don't have a login, please <a href="register.php">register</a></p>
        <p>If you are done, please <a href="inc/logout.php">log out</a>.</p>
        <p>You are currently logged <?php echo $logged ?>.</p>
    </body>
</html>
