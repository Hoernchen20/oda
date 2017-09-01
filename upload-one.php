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
    <link rel="stylesheet" type="text/css" href="js/jquery-ui-1.12.1/jquery-ui.css">
       
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
    
    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
      $( function() {
            var availableTags = [
      "ActionScript",
      "AppleScript",
      "Asp",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme"
    ];
      const MinInputLength = 2;
        function split( val ) {
          return val.split( "\n" );
        }
        function extractLast( term ) {
          return split( term ).pop();
        }
        
            $( "#author" )
              // don't navigate away from the field on tab when selecting an item
              .on( "keydown", function( event ) {
                if ( event.keyCode === $.ui.keyCode.TAB &&
                    $( this ).autocomplete( "instance" ).menu.active ) {
                  event.preventDefault();
                }
              })
              .autocomplete({
                minLength: MinInputLength,
                source: function(request, response) {
                  $.getJSON( "inc/getauthor.php", function(data) {
                  // delegate back to autocomplete, but extract the last term
                  var resp;
                  var lastTerm = extractLast(request.term);
                  if (lastTerm.length >= MinInputLength) {
                    response($.ui.autocomplete.filter(data, lastTerm));
                  }}
                )},
                
                focus: function() {
                  // prevent value inserted on focus
                  return false;
                },
                select: function( event, ui ) {
                  var terms = split( this.value );
                  // remove the current input
                  terms.pop();
                  // add the selected item
                  terms.push( ui.item.value );
                  // add placeholder to get the comma-and-space at the end
                  terms.push( "" );
                  this.value = terms.join( "\r\n" );
                  return false;
                }
              });
          } );
    </script> 
    
  </body>
</html>
