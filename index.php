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
      if(login_check() == false) {
        header('Location: login.php');
      } else {
        PrintTopNavigation("overview"):
        $UserCategories = GetUserCategories($_SESSION['user_id']);
        $ShareCategories = GetShareCategories($_SESSION['user_id']);
        $SelectCategorie = $_GET['categorie'];

        echo '<div id="navigation">';
        
        PrintCategorieBar($UserCategories);
        PrintCategorieBar($ShareCategories);
        
        echo '</div>
          <div id="wrapper">
            <div id="content">
              <div style="overflow-x:auto">';
        PrintDocumentTable($_SESSION['user_id'], $SelectCategorie);
        echo '</table>
            </div>
          </div>
        </div>';
      }
      ?>
      <div id="footer">
        <p>OpenDocumentArchiv V0.1</p>
      </div>
    </div>
  </body>
</html>
