<?php
    require '../vendor/autoload.php';

    use api\config\DatabaseConnector;

    $dbConnection = (new DatabaseConnector)->getConnection();
?>