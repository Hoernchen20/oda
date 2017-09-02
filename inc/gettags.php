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
      ['$unwind' => '$metadata.tags' ],
      ['$group' => ['_id' => '$metadata.tags'] ],
      ['$sort' => ['_id' => 1] ]
  ]);

  $tags = array();
  
  foreach($query as $document) {
      $tags[] = $document['_id'];
  }
  
  echo json_encode($tags);
}
?>
