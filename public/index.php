<?php
require "../api/config/LoadDB.php";

use api\controllers\StudentController;
use api\controllers\QualificationController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch($uri[1]){
    case 'student':
        // pass the uri to the StudentController to process the HTTP request:
        $controller = new StudentController($dbConnection, $requestMethod, $uri);
        $controller->processRequest();
        break;
    case 'qualification':
        $controller = new QualificationController($dbConnection, $requestMethod, $uri);
        $controller->processRequest();
        break;
    case 'login':
        $controller = new LoginController($dbConnection, $requestMethod);
        $controller->processRequest();
        break;
    default:
        // everything else results in a 404 Not Found
        header("HTTP/1.1 404 Not Found");
        exit(json_encode(
            array("message" => "Resource Not found.")
        ));
        break;

}
?>