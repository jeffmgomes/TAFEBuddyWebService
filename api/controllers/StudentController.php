<?php
namespace api\controllers;

use api\models\Student;

class StudentController {
    
    private $db;
    private $requestMethod;
    private $studentId;

    private $student;

    public function __construct($db, $requestMethod, $studentId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->studentId = $studentId;

        $this->student = new Student($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->studentId) {
                    $response = $this->get($this->studentId);
                } else {
                    $response = $this->getAll();
                };
                break;
            case 'POST':
                $response = $this->createStudentFromRequest();
                break;
            case 'PUT':
                $response = $this->updateStudentFromRequest($this->studentId);
                break;
            case 'DELETE':
                $response = $this->deleteStudent($this->studentId);
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
        $result = $this->student->getAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function get($id)
    {
        $result = $this->student->get($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createUserFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateStudent($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->student->insert();
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateUserFromRequest($id)
    {
        $result = $this->student->get($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateStudent($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->student->update($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteUser($id)
    {
        $result = $this->student->get($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->student->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateStudent($input)
    {
        

        if (! isset($input['firstname']) &&
            ! isset($input['lastname']) &&
            ! isset($input['email'])) {
            return false;
        } else {  
            $this->student->firstName = $input['firstname'];
            $this->student->lastName = $input['lastname'];
            $this->student->email = $input['email'];
            $this->student->password = (isset($input['password']) ? $input['password'] : "");
        }
        return true;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
?>