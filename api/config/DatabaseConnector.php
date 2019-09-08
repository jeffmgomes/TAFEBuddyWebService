<?php
namespace api\config;

class DatabaseConnector {

    private $dbConnection = null;

    public function __construct()
    {
        // Get the Connection String from the Server
        $connStr = getenv("MYSQLCONNSTR_localdb");
        // Split the string in the array
        $split = explode(";",$connStr);
        $connArray = array();
        foreach ($split as $key => $value) {
            $k = substr($value,0,strpos($value,"="));
            $connArray[$k] = substr($value,strpos($value,"=")+1);
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->dbConnection = new \mysqli(
                                    $connArray["Data Source"],
                                    $connArray["User Id"],
                                    $connArray["Password"],
                                    $connArray["Database"]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            exit('Error connecting to database');
        }
    }

    public function getConnection()
    {
        return $this->dbConnection;
    }
}
?>