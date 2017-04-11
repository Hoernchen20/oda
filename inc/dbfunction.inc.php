<?php
/*
 * dbfunction.inc.php
 * 
 * Copyright 2017 Felix <felix@felix-desktop>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */

include "dbconnect.inc.php";

function AddUser($UserData) {
  /*
   * $UserData['FirstName']
   * $UserData['LastName']
   * $UserData['email']
   * $UserData['password']
   */
   
  global $client;
  $collection = $client->oda->selectCollection('users');
  $query = $collection->insertOne(['firstname' => $UserData['FirstName'], 'lastname' => $UserData['LastName'], 'email' => $UserData['email'] , 'password' => $UserData['password']]);

  if($query->getInsertedCount == 1) {
    //if everything fine, return ObjectID of new User
    $ID = $query->getInsertedID();
    return $ID['oid'];
  } else {
    return false;
  }
}

function GetUserData($user_email) {
    global $client;
    $collection = $client->oda->selectCollection('users');
    $query = $collection->findOne(['email' => $user_email]);

    return $query;
}

function GetUserPassword($user_id) {
    global $client;
    $collection = $client->oda->selectCollection('users');
    $query = $collection->findOne(['_id' => new \MongoDB\BSON\ObjectID($user_id)]);
    return $query;
}

function GetUserCategories($user_name) {
    global $client;
    $collection = $client->oda->selectCollection('documents.files');
    $query = $collection->aggregate([
        ['$match' => ['metadata.owner' => new \MongoDB\BSON\ObjectID('58c546bf4b1c1f1e7cd7508d') ] ],
        ['$unwind' => '$metadata.categories' ],
        ['$group' => ['_id' => '$metadata.categories'] ],
        ['$sort' => ['_id' => 1] ]
    ]);

    $categories = array();
    
    foreach($query as $document) {
        $categories[] = $document['_id'];
    }
    return $categories;
}

function GetShareCategories($user_name) {
    global $client;
    $collection = $client->oda->selectCollection('documents.files');
    $query = $collection->aggregate([
        ['$match' => ['metadata.share' => new \MongoDB\BSON\ObjectID('58c546bf4b1c1f1e7cd7508d') ] ],
        ['$unwind' => '$metadata.categories' ],
        ['$group' => ['_id' => '$metadata.categories'] ],
        ['$sort' => ['_id' => 1] ]
    ]);

    $categories = array();
    
    foreach($query as $document) {
        $categories[] = $document['_id'];
    }
    return $categories;
}

function GetDocuments($UserID, $Categorie) {
    global $client;
    $collection = $client->oda->selectCollection('documents.files');
	/* TODO Abfragekriterien überprüfen */
    return $collection->find(['metadata.owner' => new \MongoDB\BSON\ObjectID('58c546bf4b1c1f1e7cd7508d')/*$UserID*/, 'metadata.categories' => $Categorie],['sort' => ['metadata.title' => 1]]);

/*    $Documents = array();
    $i = 0;
	
	/* TODO alle Zeile verarbeiten */
 /*   foreach($query as $document) {
		$Documents[$i]['id'] = $document['_id'];
        $Documents[$i]['title'] = $document['title'];
		$Documents[$i]['author'] = $document['author'];
        $Documents[$i]['tags'] = $document['tag'];
		$i++;
    }
    return $Documents;*/
}

function SaveOneDocument($Post) {
  $title = $Post['title'];
  $author = $Post['author'];
  $categories = preg_split('/\r\n|[\r\n]/', $Post['categories']);
  $tags = preg_split('/\r\n|[\r\n]/', $Post['tags']);
  $owner = new MongoDB\BSON\ObjectID($_GET['user']);
  $share = preg_split('/\r\n|[\r\n]/', $Post['share']);
  $lastmodified = ['by' => $owner, 'date' => new MongoDB\BSON\UTCDateTime()];
  $created = ['by' => $owner, 'date' => new MongoDB\BSON\UTCDateTime()];
  
  $filename = pathinfo($_FILES['datei']['name'], PATHINFO_FILENAME);
  $extension = strtolower(pathinfo($_FILES['datei']['name'], PATHINFO_EXTENSION));
  
  //Überprüfung der Dateiendung
  $allowed_extensions = array('pdf', 'jpg', 'png');
  if(!in_array($extension, $allowed_extensions)) {
      die("Ungültige Dateiendung. Nur pdf-Dateien sind erlaubt");
  }

  //Überprüfung der Dateigröße
  $max_size = 60*1024*1024; //60MB
  if($_FILES['datei']['size'] > $max_size) {
      die("Bitte keine Dateien größer 60MB hochladen");
  }
  
  //Datenbank
  global $client;
  $metadata = ['metadata' => ['title' => $title, 'author' => $author, 'categories' => $categories, 'tags' => $tags, 'owner' => $owner, 'share' => $share, 'lastmodified' => $lastmodified, 'created' => $created ] ];
  $bucket = $client->oda->selectGridFSBucket(['bucketName' => 'documents']);
  $file = fopen($_FILES['datei']['tmp_name'], 'rb');
  $bucket->uploadFromStream($filename.'.'.$extension, $file, $metadata);
}

?>

