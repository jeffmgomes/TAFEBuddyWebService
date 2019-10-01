<?php
namespace api\controllers;

use api\models\Qualification;

class QualificationController {

    private $db;
    private $requestMethod;
    private $qualCode = null;

    private $qualification;

    public function __construct($db, $requestMethod, $uri)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        if(isset($uri[2])){
            $this->qualCode = (string) $uri[2];
        }

        $this->qualification = new Qualification($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->qualCode) {
                    $response = $this->get($this->qualCode);
                } else {
                    $response = $this->getAll();
                };
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAll()
    {
        $result = $this->qualification->getAll();
        $response['status_code_header'] = "HTTP/1.1 200 OK";
        $response['body'] = json_encode($result);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode([
            'message' => 'Resource not found'
        ]);
        return $response;
    }
}

?>