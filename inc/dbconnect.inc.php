<?php
    require $_SERVER['DOCUMENT_ROOT'].'/oda/vendor/autoload.php'; // include Composer's autoloader
    
    $client = new MongoDB\Client("mongodb://192.168.50.20:27017");
    $collection = $client->oda->fs;
?>
