<?php
    require '/var/www/html/login/vendor/autoload.php'; // include Composer's autoloader
    
    $client = new MongoDB\Client("mongodb://192.168.50.20:27017");
    $collection = $client->oda->fs;
?>
