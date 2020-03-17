<?php
namespace api\controllers;

use api\models\Student;
use api\models\Lecture;

class LoginController {

    private $db;
    private $requestMethod;
    private $email;
    private $password;

    private $student;
    private $lecture;

    public function __construct($db, $requestMethod)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;

        $this->student = new Student($db);
        $this->lecture = new Lecture($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'POST':
                $response = $this->login();
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

    private function login()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateInput($input)) {
            return $this->badRequestResponse();
        } else {
            $studentResult = $this->student->login($this->email,$this->password);
            $lectureResult = $this->lecture->login($this->email,$this->password);

            if ($studentResult) {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['body'] = json_encode([
                    'type' => 'Student',
                    'value' => $studentResult
                ]);
                return $response;
            }
            
            if ($lectureResult) {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['body'] = json_encode([
                    'type' => 'Lecture',
                    'value' => $lectureResult
                ]);
                return $response;
            }

            $response['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
            $response['body'] = json_encode([
                'message' => 'Credentials you entered were invalid'
            ]);
            return $response;
        }
    }

    private function validateInput($input)
    {
        if(! isset($input['email']) &&
           ! isset($input['password'])) {
            return false;
        } else {
            $this->email = $input['email'];
            $this->password = $input['password'];
        }
        return true;
    }

    private function badRequestResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
        $response['body'] = json_encode([
            'message' => 'Bad Request'
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