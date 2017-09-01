<?php
include "inc/loginfunction.inc.php";
include "inc/htmlfunction.inc.php";
sec_session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>OpenDocumentArchiv - Upload</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script type="text/JavaScript" src="js/jquery-3.2.1.min.js"></script>
    <script>
      $( function() {
        $( "author" ).autocomplete({
          source: "inc/getauthor.php"
        });
      } );
    </script>    
  </head>
  <body>
    <div id="container">
      <?php 
      if(login_check() == false) {        
        header('Location: login.php');
      } else {
        PrintTopNavigation("upload");
        echo '<div id="navigation">
          <ul>
            <li><a class="active">Einzel Upload</a></li>
            <li><a href="index.php?site=uploadmulti">Multi Upload</a></li>
            <li><a href="index.php?site=uploadlast">Letzen 10 Uploads</a></li>
          </ul>
        </div>';
        if (!empty($_FILES)) {
          SaveOneDocument($_POST);
        }
        echo '<div id="wrapper">
          <div id="content">
            <form action="#" method="post" enctype="multipart/form-data">
              <label for="title" >Titel</label>
              <input type="text" id="title" name="title">
              <label for="author">Author</label>
              <textarea id="author" id="author" name="author"></textarea>
              <label for="categories">Kategorien</label>
              <textarea id ="categories" name="categories"></textarea>
              <label for="tags">Tags</label>
              <textarea id="tags" name="tags"></textarea>
              <label for="share">share</label>
              <textarea id="share" name="share"></textarea>
              <label for="file">Dateiauswahl</label>
              <input type="file" id="file" name="datei">
              <input type="submit" value="Hochladen">
            </form>
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
