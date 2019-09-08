<?php
namespace api\models;

class Student{

    private $db = null;
    private $tableName = "student";

    // Properties
    public $id;
    public $firstName;
    public $lastName;
    public $email;
    public $password;


    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $stmt = "
            SELECT 
                id, firstname, lastname, email, password
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
                id, firstname, lastname, email, password
            FROM
                ". $this->tableName ."
            WHERE id = ?;
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
                (firstname, lastname, email, password)
            VALUES
                (:firstname, :lastname, :email, :password);
        ";

        try {
            $stmt = $this->db->prepare($stmt);

            // sanitize
            $this->sanitize();

            // bind values
            $stmt->bind_param(":firstname", $this->firstname);
            $stmt->bind_param(":lastname", $this->lastname);
            $stmt->bind_param(":email", $this->email);
            $stmt->bind_param(":password", $this->password);

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
                firstname = :firstname,
                lastname  = :lastname,
                email = :email,
                password = :password
            WHERE id = :id;
        ";

        try {
            $stmt = $this->db->prepare($stmt);
            // sanitize
            $this->sanitize();

            // bind values
            $stmt->bind_param(":firstname", $this->firstname);
            $stmt->bind_param(":lastname", $this->lastname);
            $stmt->bind_param(":email", $this->email);
            $stmt->bind_param(":password", $this->password);
            $stmt->bind_param(":id", $id);

            $stmt->execute();

            return $stmt->affected_rows;

        } catch (Exception $e) {
            exit($e->getMessage());
        }    
    }

    public function delete($id)
    {
        $stmt = "
            DELETE FROM person
            WHERE id = :id;
        ";

        try {
            $stmt = $this->db->prepare($stmt);

            // bind values
            $stmt->bind_param(":id", $id);

            $stmt->execute();

            return $stmt->affected_rows;

        } catch (Exception $e) {
            exit($e->getMessage());
        }    
    }

    private function sanitize(){
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->firstName = htmlspecialchars(strip_tags($this->firstName));
        $this->lastName = htmlspecialchars(strip_tags($this->lastName));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
    }
}
?>