<?php
namespace api\models;

class Student{

    private $db = null;
    private $tableName = "student";

    // Properties
    public $studentId;
    public $givenName;
    public $lastName;
    public $emailAddress;
    public $password;


    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $stmt = "
            SELECT 
                `StudentID`, `GivenName`, `LastName`, `EmailAddress`
            FROM
                " . $this->tableName . ";
        ";

        try {
            $stmt = $this->db->prepare($stmt); // Prepare the query
            $stmt->execute(); // Execute the query
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Get the result
            $stmt->close(); // Close the connection
            return $result;
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

    public function get($id)
    {
        $stmt = "
            SELECT 
                `StudentID`, `GivenName`, `LastName`, `EmailAddress`
            FROM
                ". $this->tableName ."
            WHERE StudentID = ?;
        ";

        try {
            $stmt = $this->db->prepare($stmt);
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc(); // Get a single row
            $stmt->close(); // Close the connection
            return $result;
        } catch (Exception $e) {
            exit($e->getMessage());
        }    
    }

    public function insert()
    {
        $stmt = "
            INSERT INTO ". $this->tableName ." 
            (`StudentID`, `GivenName`, `LastName`, `EmailAddress`, `Password`)
            VALUES
                (?, ?, ?, ?, SHA2(?,224));
        ";

        try {
            $stmt = $this->db->prepare($stmt);

            // sanitize
            $this->sanitize();

            // bind values
            $stmt->bind_param("ssssb", $this->studentId, $this->givenName, $this->lastName, $this->emailAddress, $this->password);

            $stmt->execute();

            return $stmt->affected_rows;

        } catch (Exception $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id)
    {
        $stmt = "
            UPDATE ". $this->tableName ."
            SET 
                GivenName = ?,
                LastName  = ?,
                EmailAddress = ?,
                Password = ?
            WHERE StudentID = ?;
        ";

        try {
            $stmt = $this->db->prepare($stmt);
            // sanitize
            $this->sanitize();

            // bind values
            $stmt->bind_param("sssss", $this->givenName, $this->lastName, $this->emailAddress, $this->password, $id);

            $stmt->execute();

            return $stmt->affected_rows;

        } catch (Exception $e) {
            exit($e->getMessage());
        }    
    }

    public function delete($id)
    {
        $stmt = "
            DELETE FROM " . $this->tableName . "
            WHERE StudentID = ?;
        ";

        try {
            $stmt = $this->db->prepare($stmt);

            // bind values
            $stmt->bind_param("i", $id);

            $stmt->execute();

            return $stmt->affected_rows;

        } catch (Exception $e) {
            exit($e->getMessage());
        }    
    }

    private function login($email, $password)
    {
        $stmt = "
            SELECT * FROM " . $this->tableName . "
            WHERE EmailAddress = ?
                AND Password = SHA2(?,224);
        ";

        try {
            $stmt = $this->db->prepare($stmt);

            // bind values
            $stmt->bind_param("ss", $email, $password);

            $stmt->execute();

            return $stmt->affected_rows;

        } catch (Exception $e) {
            exit($e->getMessage());
        }    
    }

    private function sanitize(){
        $this->studentId = htmlspecialchars(strip_tags($this->studentId));
        $this->givenName = htmlspecialchars(strip_tags($this->givenName));
        $this->lastName = htmlspecialchars(strip_tags($this->lastName));
        $this->emailAddress = htmlspecialchars(strip_tags($this->emailAddress));
        $this->password = htmlspecialchars(strip_tags($this->password));
    }
}
?>