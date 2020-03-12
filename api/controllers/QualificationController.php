<?php
namespace api\controllers;

use api\models\Qualification;

class QualificationController {

    private $db;
    private $requestMethod;
    private $qualCode = null;
    private $tafeCompCode = null;
    private $function = null;

    private $qualification;

    public function __construct($db, $requestMethod, $uri)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        if(isset($uri[2])){
            $this->qualCode = (string) $uri[2];
        }

        if (isset($uri[3])) {
            $this->function = (string) $uri[3];
        }

        if( isset($uri[4])) {
            $this->tafeCompCode = (string) $uri[4];
        }

        $this->qualification = new Qualification($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->qualCode) {
                    if ($this->tafeCompCode) {
                        $response = $this->processFunctions();
                    } else {
                        $response = $this->get();
                    }
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

    // Functions Processor
    private function processFunctions() 
    {
        switch ($this->function) {
            case 'subjectsuggestions':
                $response = $this->getSuggestionForSubject();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        return $response;
    }

    private function getAll()
    {
        $result = $this->qualification->getAll();
        $response['status_code_header'] = "HTTP/1.1 200 OK";
        $response['body'] = json_encode($result);
        return $response;
    }

    private function get()
    {
        $result = $this->qualification->get($this->qualCode);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
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

    private function getSuggestionForSubject()
    {
        $result = $this->qualification->get($this->qualCode);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->qualification->qualCode = $this->$qualCode;
        $result = $this->qualification->getSuggestionForSubject($this->tafeCompCode);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
}

?>