<?php
namespace api\controllers;

use api\models\Student;

class StudentController {
    
    private $db;
    private $requestMethod;
    private $studentId = null;
    private $function = null;
    private $qualCode = null;

    private $student;

    public function __construct($db, $requestMethod, $uri)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        if (isset($uri[2])) {
            $this->studentId = (string) $uri[2];
        }

        if (isset($uri[3])) {
            $this->function = (string) $uri[3];
        }

        if( isset($uri[4])) {
            $this->qualCode = (string) $uri[4];
        }

        $this->student = new Student($db);
    }

    // Main request processor
    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->studentId) {
                    if($this->function) {
                        $response = $this->processFunctions();
                    } else {
                        $response = $this->get();
                    }                    
                } else {
                    $response = $this->getAll();
                };
                break;
            case 'POST':
                $response = $this->createStudentFromRequest();
                break;
            case 'PUT':
                $response = $this->updateStudentFromRequest();
                break;
            case 'DELETE':
                $response = $this->deleteStudent();
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

    // Main functions
    private function getAll()
    {
        $result = $this->student->getAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function get()
    {
        $result = $this->student->get($this->studentId);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createStudentFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateStudent($input)) {
            return $this->unprocessableEntityResponse();
        }
        $result = $this->student->insert();
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode([
            'message' => $result
        ]);
        return $response;
    }

    private function updateStudentFromRequest()
    {
        $result = $this->student->get($this->studentId);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateStudent($input)) {
            return $this->unprocessableEntityResponse();
        }
        $result = $this->student->update($this->studentId);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'message' => $result
        ]);
        return $response;
    }

    private function deleteStudent()
    {
        $result = $this->student->get($this->studentId);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $result = $this->student->delete($this->studentId);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'message' => $result
        ]);
        return $response;
    }

    // Validation functions
    private function validateStudent($input)
    {
        if (! isset($input[0]['firstname']) &&
            ! isset($input[0]['lastname']) &&
            ! isset($input[0]['email'])) {
            return false;
        } else {  
            $this->student->firstName = $input[0]['firstname'];
            $this->student->lastName = $input[0]['lastname'];
            $this->student->email = $input[0]['email'];
            $this->student->password = (isset($input[0]['password']) ? $input[0]['password'] : "");
        }
        return true;
    }

    // Functions Processor
    private function processFunctions() 
    {
        switch ($this->function) {
            case 'qualifications':
                $response = $this->getQualifications();
                break;
            case 'results':
                $response = $this->getResults();
                break;
            case 'resultsv2':
                if ($this->qualCode) {
                    $response = $this->getResultsV2($qualCode);
                } else {
                    $response = $this->notFoundResponse();
                }
                break;  
            default:
                $response = $this->notFoundResponse();
                break;
        }
        return $response;
    }

    // Student Functions
    private function getQualifications() 
    {
        $result = $this->student->get($this->studentId);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->student->studentId = $this->studentId;
        $result = $this->student->getQualifications();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getResults()
    {
        $result = $this->student->get($this->studentId);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->student->studentId = $this->studentId;
        $result = $this->student->getResults();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getResultsV2($qualCode)
    {
        $result = $this->student->get($this->studentId);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->student->studentId = $this->studentId;
        $result = $this->student->getResultsV2($qualCode);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    // Miscellaneous functions
    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'message' => 'Invalid input'
        ]);
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