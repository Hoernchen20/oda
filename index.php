<?php
include "inc/loginfunction.inc.php";
include "inc/htmlfunction.inc.php";
sec_session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>OpenDocumentArchiv</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="styles.css">
  </head>
  <body>
    <div id="container">
      <?php 
      if(login_check() == true) {        
        switch($_GET['site']) {
          case "uploadone":
            PrintUploadOneSite($_GET);
            break;
          case "uploadmulti":
            PrintUploadMultiSite($_GET);
            break;
          case "uploadlast":
            PrintUploadLastSite($_GET);
            break;
          case "search":
            PrintSearch($_GET);
            break;
          case "settings":
            PrintSettings($_GET);
            break;
          case "overview":
          default:
            PrintOverview($_GET);
        }
      } else {
         header('Location: login.php');
      }
      ?>
      <div id="footer">
        <p>OpenDocumentArchiv V0.1</p>
      </div>
    </div>
  </body>
</html>
