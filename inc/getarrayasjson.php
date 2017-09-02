<?php
include "loginfunction.inc.php";
include "htmlfunction.inc.php";
sec_session_start();

if(login_check() == false) {        
  header('Location: login.php');
} else {
  if($_GET['typ'] == 'author') {
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
  } else if($_GET['typ'] == 'categories') {
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
  } else if($_GET['typ'] == 'tags') {
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
  } else {
    $error = 'No Data';
    echo json_encode($error);
}
?>
