<?php
    require '/var/www/html/vendor/autoload.php'; // include Composer's autoloader
    
    $client = new MongoDB\Client("mongodb://127.0.0.1:27017");
    $collection = $client->oda->fs;
?>
