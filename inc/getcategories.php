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
      ['$unwind' => '$metadata.categories' ],
      ['$group' => ['_id' => '$metadata.categories'] ],
      ['$sort' => ['_id' => 1] ]
  ]);

  $categories = array();
  
  foreach($query as $document) {
      $categories[] = $document['_id'];
  }
  
  echo json_encode($categories);
}
?>
