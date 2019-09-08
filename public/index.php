<?php
require "../api/config/LoadDB.php";

use api\controllers\StudentController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// all of our endpoints start with /person
// everything else results in a 404 Not Found
if ($uri[1] !== 'student') {
    header("HTTP/1.1 404 Not Found");
    exit(json_encode(
        array("message" => "Not found. 0 - " . $uri[2] . " 1 - " . $uri[3])
    ));
}

// the user id is, of course, optional and must be a number:
$userId = null;
if (isset($uri[2])) {
    $userId = (int) $uri[2];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the StudentController and process the HTTP request:
$controller = new StudentController($dbConnection, $requestMethod, $userId);
$controller->processRequest();

?>