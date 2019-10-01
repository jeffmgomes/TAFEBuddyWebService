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

    public function login($email, $password)
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
            $result = $stmt->get_result()->fetch_assoc(); // Get a single row
            $stmt->close(); // Close the connection
            return $result;
        } catch (Exception $e) {
            exit($e->getMessage());
        }    
    }

    public function getQualifications() {
        $stmt = "
            SELECT * FROM `student_studyplan`
            INNER JOIN qualification ON student_studyplan.QualCode = qualification.QualCode
            WHERE student_studyplan.StudentID = ?;
        ";
        
        try {
            $stmt = $this->db->prepare($stmt); // Prepare the query
            $stmt->bind_param("s", $this->studentId);
            $stmt->execute(); // Execute the query
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Get the result
            $stmt->close(); // Close the connection
            return $result;
        } catch (Exception $e) {
            exit($e->getMessage());
        }

    }

    public function getResults()
    {
        $stmt = "
                SELECT qualification.QualCode, student_grade.CRN, student_grade.TafeCompCode, student_grade.TermCode, student_grade.TermYear, 
                student_grade.Grade, student_grade.GradeDate, subject.SubjectCode, subject.SubjectDescription, competency.NationalCompCode, competency.CompetencyName 
                FROM student_grade 
                INNER JOIN student_studyplan ON student_grade.StudentID = student_studyplan.StudentID 
                INNER JOIN qualification ON student_studyplan.QualCode = qualification.QualCode 
                INNER JOIN crn_detail ON crn_detail.CRN = student_grade.CRN 
                INNER JOIN subject ON subject.SubjectCode = crn_detail.SubjectCode 
                INNER JOIN competency ON competency.TafeCompCode = crn_detail.TafeCompCode 
                WHERE student_grade.StudentID = ?;
        ";
        try {
            $stmt = $this->db->prepare($stmt); // Prepare the query
            $stmt->bind_param("s", $this->studentId);
            $stmt->execute(); // Execute the query
            $result = $stmt->get_result(); // Get the result

            // Grouping result
            $arr = [];
            $firstColName = $result->fetch_field_direct(0)->name; // Name of the first column
            // Group by Qualification
            while($row = $result->fetch_assoc()) {
                $firstColVal = $row[$firstColName];
                unset($row[$firstColName]);
                $arr[$firstColVal][] = $row;
            }

            $stmt->close(); // Close the connection
            return $arr; // Return the group array
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