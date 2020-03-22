<?php
require "../api/config/LoadDB.php";

use api\controllers\StudentController;
use api\controllers\QualificationController;
use api\controllers\LoginController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// authenticate the request with Okta:
$arrayOfClients = explode(',',getenv('OKTALISTOFCLIENTID'));
$isValid = false;
foreach ($arrayOfClients as $clientId) {
    $isValid = authenticate($clientId);
}

if (! $isValid) {
    header("HTTP/1.1 401 Unauthorized");
    exit('Unauthorized');
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch($uri[1]){
    case 'students':
        // pass the uri to the StudentController to process the HTTP request:
        $controller = new StudentController($dbConnection, $requestMethod, $uri);
        $controller->processRequest();
        break;
    case 'qualifications':
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

function authenticate($clientId) {
    try {
        switch(true) {
            case array_key_exists('HTTP_AUTHORIZATION', $_SERVER) :
                $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
                break;
            case array_key_exists('Authorization', $_SERVER) :
                $authHeader = $_SERVER['Authorization'];
                break;
            default :
                $authHeader = null;
                break;
        }
        preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
        if(!isset($matches[1])) {
            throw new \Exception('No Bearer Token');
        }
        $jwtVerifier = (new \Okta\JwtVerifier\JwtVerifierBuilder())
        ->setIssuer(getenv('OKTAISSUER'))
        ->setAudience('api://default')
        ->setClientId($clientId)
        ->build();
        return $jwtVerifier->verify($matches[1]);
    } catch (\Exception $e) {
        return false;
    }
}
?>