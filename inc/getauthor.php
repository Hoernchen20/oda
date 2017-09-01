<?php
include "loginfunction.inc.php";
include "htmlfunction.inc.php";
sec_session_start();

if(login_check() == false) {        
  header('Location: login.php');
} else {
  global $client;
  $collection = $client->oda->selectCollection('documents.files');
  $query = $collection->aggregate([
      ['$match' => ['metadata.owner' => new \MongoDB\BSON\ObjectID($_SESSION['user_id']) ] ],
      ['$unwind' => '$metadata.author' ],
      ['$group' => ['_id' => '$metadata.author'] ],
      ['$sort' => ['_id' => 1] ]
  ]);

  $author = array();
  
  foreach($query as $document) {
      $author[] = $document['_id'];
  }
  
  echo json_encode($author);
}
?>
