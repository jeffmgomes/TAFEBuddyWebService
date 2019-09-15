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

}
?>