<?php

function PrintRow($Row, $LastRow) {
  echo "<hr>";
  echo '<p>$LastRow: ' . $LastRow . '</p>';
  echo '<p>$Row: ' . $Row . '</p>';

  $LastMenuPoint = '';

  // keine Untermenüs -> Menü direkt ausgeben
  if ( strpos($Row, '|') === FALSE ) {
    echo "<li>" . $Row . "</li>\n";
    $LastRow .= $Row;
    return $LastRow;
  // Untermenüs vorhanden -> Menüstruktur zerlegen
  } else {
    //Menüpunkt extrahieren
    $menupoint = strstr($Row, '|', true);

    if ($menupoint != $LastRow) {
      $LastMenuPoint .= $menupoint;
      echo "<li>" . $menupoint . "</li>\n";
    }

    //ausgegebenen Menüpunkt abziehen
    $menupoint .= '|';
    $LastMenuPoint .= $menupoint;
    $MenuPointRest = str_replace( $menupoint , '', $Row);

    if ( empty($MenuPointRest) == false ) {
      echo "<li>\n<ul>\n";
      $LastRow = PrintRow($MenuPointRest, $LastMenuPoint);
      echo "</ul>\n</li>\n";
    }
    return $LastRow;
  }
}

function PrintCategorieBar($aCategories) {
  $ebene = 1;
  $nCntCategories = count($aCategories);

  //Strings in Arrays umwandeln
  for ($nIndexCategorie = 0; $nIndexCategorie < $nCntCategories; $nIndexCategorie++) {
    $aCategories[$nIndexCategorie] = explode('|', $aCategories[$nIndexCategorie]);
  }

/*	echo '<pre>';
  var_dump($aCategories);
  echo '</pre>';
  echo '<hr>';*/

  echo "<ul>\n";


  for ($nIndexCategorie = 0; $nIndexCategorie < $nCntCategories; $nIndexCategorie++) {
    $nCntSubCategories = count($aCategories[$nIndexCategorie]);

/*		echo '<li>$ebene: ' . $ebene . "</li>";
    echo '<li>$nCntSubCategories: ' . $nCntSubCategories . "</li>";*/

    //befinden wir uns in einer tieferen Ausgabeebene als die aktuelle Kategorie Ebenen hat, wird die Arbeitsebene nach und nach geschlossen
    while ($nCntSubCategories < $ebene) {
      echo "</ul></li>\n";
      $ebene--;
    }

    //Indexfehler für die erste Kategorie abfangen
    if ($nIndexCategorie > 0) {
      //ist die erste Subkategorie der Kategorie anders, werden die Arbeitsebene bis zur ersten Kategorieebene geschlossen	
      if ( ($aCategories[$nIndexCategorie][0] != $aCategories[$nIndexCategorie-1][0]) ) {
	while ($ebene > 1) {
	  echo "</ul></li>\n";
	  $ebene--;
	}
      }
    }

    for ($j = 0; $j < $nCntSubCategories; $j++) {
      /* ist die Subkategorie anders als die Subkategorie der letzten Kategorie
       * und befinden wir und in der richtigen Arbeitsebene, wird die Subkategorie ausgegeben */
      if ( ($aCategories[$nIndexCategorie][$j] != $aCategories[$nIndexCategorie-1][$j]) AND ($ebene-1 == $j) ) {
	echo "<li>" . $aCategories[$nIndexCategorie][$j] . "</li>\n";
      }

      //hat die aktuelle Subkategorie mehr Ebenen als die Arbeitsebene, wird Arbeitseben um eins erhöht
      if ($nCntSubCategories > $ebene) {
	echo "<li><ul>\n";
	$ebene++;
      }
    }
  }

  //nach der letzten Kategorie die Arbeitsebene wieder auf 1 bringen und so alle HTML Listen-Elemente schliessen
  while ($ebene > 1) {
    echo "</ul></li>\n";
    $ebene--;
  }

  echo "</ul>\n";
}

function PrintDocumentTable($UserID, $Categorie) {
  $Documents = GetDocuments($UserID, $Categorie);
  echo '<table>
	  <tr>
	    <th>Titel</th>
	    <th>Author</th>
	    <th>Kategorien</th>
	    <th>Tags</th>
	    <th>Besitzer</th>
	    <th>Share</th>
	    <th>Zuletzt Bearbeitet</th>
	    <th>Erstellt</th>
	  </tr>';
					  
  /* TODO als schleife ausführen und hinter jede komplette Zeile die _id als link hinterlegen */
  /* TODO Besitzer und Datum als Klartext anzeigen */
  foreach($Documents as $document) {
/*highlight_string("<?php\n\$data =\n" . var_export($document, true) . ";\n?>");*/
/*    echo '<tr> 
	    <td>' . $document['metadata']['title'] . '</td>
	    <td>' . $document['metadata']['author'] . '</td>';*/
    echo '<tr>';
    PrintStringAsCellWhithLink($document['metadata']['title'], $document['_id']);
    PrintArrayAsCellWhithLink($document['metadata']['author'], $document['_id']);
    PrintArrayAsCellWhithLink($document['metadata']['categories'], $document['_id']);
    PrintArrayAsCellWhithLink($document['metadata']['tags'], $document['_id']);
    PrintArrayAsCellWhithLink($document['metadata']['owner'], $document['_id']);
    PrintArrayAsCellWhithLink($document['metadata']['share'], $document['_id']);
    PrintArrayAsCellWhithLink($document['metadata']['lastmodified'], $document['_id']);
    PrintArrayAsCellWhithLink($document['metadata']['created'], $document['_id']);
    echo '</tr>';
  }
}

function PrintTopNavigation($ActiveMenu){
  error_log($ActiveMenu);
  echo '<div id="header">
	  <ul>
	    <li class="logo">OpenDocumentArchiv</li>';
  
  if ($ActiveMenu == "upload") {
    echo '<li><a href="index.php">Übersicht</a></li>
	  <li><a class="active">Upload</a></li>
	  <li><a href="search.php">Suchen</a></li>
	  <li><a href="settings.php">Einstellungen</a></li>';
  } else if ($ActiveMenu == "search") {
    echo '<li><a href="index.php">Übersicht</a></li>
	  <li><a href="upload-one.php">Upload</a></li>
	  <li><a class="active">Suchen</a></li>
	  <li><a href="settings.php">Einstellungen</a></li>';
  } else if ($ActiveMenu == "settings") {
    echo '<li><a href="index.php">Übersicht</a></li>
	  <li><a href="upload-one.php">Upload</a></li>
	  <li><a href="search.php">Suchen</a></li>
	  <li><a class="active">Einstellungen</a></li>';
  } else { //overview
    echo '<li><a class="active">Übersicht</a></li>
	  <li><a href="upload-one.php">Upload</a></li>
	  <li><a href="search.php">Suchen</a></li>
	  <li><a href="settings.php">Einstellungen</a></li>';
  }
  
  echo '<li><a href="logout.php">Logout</a></li>
      </ul>
    </div>';
}

function PrintUploadMultiSite() {
  PrintUploadTopNavigation();
		
  echo '<div id="navigation">
	  <ul>
	    <li><a href="index.php?site=uploadone">Einzel Upload</a></li>
	    <li><a class="active">Multi Upload</a></li>
	    <li><a href="index.php?site=uploadlast">Letzen 10 Uploads</a></li>
	  </ul>
	</div>';
}

function PrintUploadLastSite() {
  PrintUploadTopNavigation();
		
  echo '<div id="navigation">
	  <ul>
	    <li><a href="index.php?site=uploadone">Einzel Upload</a></li>
	    <li><a href="index.php?site=uploadmulti">Multi Upload</a></li>
	    <li><a class="active">Letzen 10 Uploads</a></li>
	  </ul>
	</div>';
}

function PrintArrayAsCell($Array) {
	echo '<td>';
	$out = '';
    foreach ($Array as $key) {
        // Kein Trennstring vor dem allerersten Wert, daher der bedingte Ausdruck.
        $out .= ($out!=='' ? ", $key" : "$key");
    }
    echo $out;
	echo '</td>';
}

function PrintArrayAsCellWhithLink($Array, $Link) {
	echo '<td>
	        <a href="documentdetail.php?id=' . $Link . '">';
	$out = '';
    foreach ($Array as $key) {
        // Kein Trennstring vor dem allerersten Wert, daher der bedingte Ausdruck.
        $out .= ($out!=='' ? ", $key" : "$key");
    }
    echo $out;
	echo '</a>
	    </td>';
}

function PrintStringAsCellWhithLink($String, $Link) {
	echo '<td>
	        <a href="documentdetail.php?id=' . $Link . '">
		   ' . $String . '	
                </a>
	    </td>';
}
/* Backup
  function PrintRow($Row, $LastRow) {
	echo "<hr>";
	echo '<p>$LastRow: ' . $LastRow . '</p>';
	echo '<p>$Row: ' . $Row . '</p>';
	
	// keine Untermenüs -> Menü direkt ausgeben
	if ( strpos($Row, '|') === FALSE ) {
		echo "<li>" . $Row . "</li>\n";
		return $Row;
	
	// Untermenüs vorhanden -> Menüstruktur zerlegen
	} else {
		
		//Menüpunkt extrahieren
		$menupoint = strstr($Row, '|', true);
		
		if ($menupoint != $LastRow) {
			$LastRow = $menupoint;
			echo "<li>" . $menupoint . "</li>\n";
		}	
			//ausgegebenen Menüpunkt abziehen
			$menupoint .= '|';
			
			$tmp = str_replace( $menupoint , '', $Row);
			if (isset($tmp)) {
				echo "<li>\n<ul>\n";
				PrintRow($tmp, $LastRow);
				echo "</ul>\n</li>\n";
			}
			return $LastRow;
		
	}

}

function PrintCategorieBar($Categories) {
  echo "<ul>\n";

  $LastRow = '';
	
  foreach ($Categories as $Row) {
		$LastRow = PrintRow($Row, $LastRow);
	}
	echo "</ul>\n";
} */
?>
