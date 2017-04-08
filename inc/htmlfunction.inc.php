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
//		var_dump($document);
		echo '<tr> 
				<td>' . $document['metadata']['title'] . '</td>
				<td>' . $document['metadata']['author'] . '</td>';
		PrintArrayAsCell($document['metadata']['categories']);
		PrintArrayAsCell($document['metadata']['tags']);
		PrintArrayAsCell($document['metadata']['owner']);
		PrintArrayAsCell($document['metadata']['share']);
		PrintArrayAsCell($document['metadata']['lastmodified']);
		PrintArrayAsCell($document['metadata']['created']);
		echo '</tr>';
    }
}

function PrintOverview($Get) {
	$GetUser = CheckGetUser($_SESSION['user_id']);
	$UserCategories = GetUserCategories($GetUser);
	$ShareCategories = GetShareCategories($GetUser);
	$SelectCategorie = $_GET['categorie'];

	echo '<div id="header">
			<ul>
			  <li class="logo">OpenDocumentArchiv</li>
			  <li><a class="active">Übersicht</a></li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=uploadone">Upload</a></li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=search">Suchen</a></li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=settings">Einstellungen</a></li>
			</ul>
		  </div>
		  <div id="navigation">';
	
	PrintCategorieBar($UserCategories);
	PrintCategorieBar($ShareCategories);
	
    
    echo '</div>
					<div id="wrapper">
					<div id="content">
					<div style="overflow-x:auto">';
	PrintDocumentTable($GetUser, $SelectCategorie);
	echo '			</table>
					</div>
					</div>
					</div>';
	}

function PrintUploadTopNavigation($UserID) {
	echo '<div id="header">
			<ul>
			  <li class="logo">OpenDocumentArchiv</li>
			  <li><a href="index.php?user=' . $UserID . '&amp;site=overview">Übersicht</a></li>
			  <li><a class="active">Upload</a></li>
			  <li><a href="index.php?user=' . $UserID . '&amp;site=search">Suchen</a></li>
			  <li><a href="index.php?user=' . $UserID . '&amp;site=settings">Einstellungen</a></li>
			</ul>
		  </div>';
}

function PrintUploadOneSite($Get) {
	$GetUser = CheckGetUser($_GET['user']);
	
	PrintUploadTopNavigation($GetUser);
		
	echo '<div id="navigation">
			<ul>
			  <li><a class="active">Einzel Upload</a></li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=uploadmulti">Multi Upload</a></li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=uploadlast">Letzen 10 Uploads</a></li>
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
			  <input type="text" id="author" name="author">
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

function PrintUploadMultiSite($Get) {
	$GetUser = CheckGetUser($_GET['user']);
	
	PrintUploadTopNavigation($GetUser);
		
	echo '<div id="navigation">
			<ul>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=uploadone">Einzel Upload</a></li>
			  <li><a class="active">Multi Upload</a></li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=uploadlast">Letzen 10 Uploads</a></li>
			</ul>
	      </div>';
}

function PrintUploadLastSite($Get) {
	$GetUser = CheckGetUser($_GET['user']);
	
	PrintUploadTopNavigation($GetUser);
		
	echo '<div id="navigation">
			<ul>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=uploadone">Einzel Upload</a></li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=uploadmulti">Multi Upload</a></li>
			  <li><a class="active">Letzen 10 Uploads</a></li>
			</ul>
	      </div>';
}


function PrintSearch($Get) {
	$GetUser = CheckGetUser($_GET['user']);
	
	echo '<div id="header">
			<ul>
			  <li class="logo">OpenDocumentArchiv</li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=overview">Übersicht</a></li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=uploadone">Upload</a></li>
			  <li><a class="active">Suchen</a></li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=settings">Einstellungen</a></li>
			</ul>
		  </div>
		  <div id="navigation">
		  <p>Variable Suchkriterien</p>
			<ul>
			  <li>Platzhalter</li>
			</ul>
	      </div>';
	}

function PrintSettings($Get) {
	$GetUser = CheckGetUser($_GET['user']);
	
	echo '<div id="header">
			<ul>
			  <li class="logo">OpenDocumentArchiv</li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=overview">Übersicht</a></li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=uploadone">Upload</a></li>
			  <li><a href="index.php?user=' . $GetUser . '&amp;site=search">Suchen</a></li>
			  <li><a class="active">Einstellungen</a></li>
			</ul>
		  </div>
		  <div id="navigation">
			<ul>
			  <li>Platzhalter</li>
			</ul>
	      </div>';
	}

function CheckGetUser($Get) {
	if ( !(ctype_alnum($Get)) ) {
	  exit('Error: User_Parameter');
	} else {
	  return $Get;
	}
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
