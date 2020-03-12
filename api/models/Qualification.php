<?php
namespace api\models;

class Qualification {
    private $db = null;
    private $tableName = "qualification";

    // Properties
    public $qualCode;
    public $nationalCode;
    public $tafeCode;
    public $name;
    public $totalUnits;
    public $coreUnits;
    public $electedUnits;
    public $recListElectedUnits;

    // Constructor
    public function __construct($db){
        $this->db = $db;
    }

    public function get($code) {
        $stmt = "SELECT 
        `QualCode`, `NationalQualCode`, `TafeQualCode`, `QualName`, `TotalUnits`, `CoreUnits`, `ElectedUnits`, `ReqListedElectedUnits` 
        FROM 
            " . $this->tableName . "
        WHERE QualCode = ? OR NationalQualCode = ?; 
        ";
        try {
            $stmt = $this->db->prepare($stmt);
            $stmt->bind_param("ss", $code, $code);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc(); // Get a single row
            $stmt->close(); // Close the connection
            return $result;
        } catch (Exception $e) {
            exit($e->getMessage());
        }   
    }

    public function getAll(){
        $stmt = "SELECT 
                `QualCode`, `NationalQualCode`, `TafeQualCode`, `QualName`, `TotalUnits`, `CoreUnits`, `ElectedUnits`, `ReqListedElectedUnits` 
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

    public function getSuggestionForSubject($tafeCompCode){
        $stmt = "SELECT 
                subject.SubjectCode, subject.SubjectDescription, subject_competency.TafeCompCode, subject_qualification.UsageType, subject_qualification.QualCode 
                FROM `subject_competency` 
                INNER JOIN subject_qualification ON subject_competency.SubjectCode = subject_qualification.SubjectCode 
                INNER JOIN subject ON subject_qualification.SubjectCode = subject.SubjectCode 
                WHERE subject_competency.TafeCompCode = ? 
                AND subject_qualification.QualCode = ?;
        ";
        try {
            $stmt = $this->db->prepare($stmt); // Prepare the query
            $stmt->bind_param("ss", $tafeCompCode, $this->qualCode);
            $stmt->execute(); // Execute the query
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Get the result
            $stmt->close(); // Close the connection
            return $result;
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

}
?>