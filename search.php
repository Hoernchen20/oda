<?php
include "inc/loginfunction.inc.php";
include "inc/htmlfunction.inc.php";
sec_session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>OpenDocumentArchiv - Search</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="styles.css">
  </head>
  <body>
    <div id="container">
      <?php 
      if(login_check() == false) {        
        header('Location: login.php');
      } else {
        PrintTopNavigation("search");
        echo '<div id="navigation">
		<p>Variable Suchkriterien</p>
		<ul>
		  <li>Platzhalter</li>
		</ul>
	      </div>';
      }
      ?>
      <div id="footer">
        <p>OpenDocumentArchiv V0.1</p>
      </div>
    </div>
  </body>
</html>
