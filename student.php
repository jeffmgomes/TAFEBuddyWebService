<?php
// Create connection

$connStr = getenv("MYSQLCONNSTR_localdb");

$split = explode(";",$connStr);
$connArray = array();
foreach ($split as $key => $value) {
  $k = substr($value,0,strpos($value,"="));
  $connArray[$k] = substr($value,strpos($value,"=")+1);
}

$con=mysqli_connect($connArray["Data Source"],$connArray["User Id"],$connArray["Password"],$connArray["Database"]);
 
// Check connection
if (mysqli_connect_errno()){
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  echo $connStr;
  print_r($connArray);
}
 
// This SQL statement selects ALL from the table 'Locations'
$sql = "SELECT * FROM Student";
 
// Check if there are results
if ($result = mysqli_query($con, $sql)){
    // If so, then create a results array and a temporary one
    // to hold the data
    $resultArray = array();
    $tempArray = array();
 
    // Loop through each row in the result set
    while ($row = $result->fetch_object()) {
        $tempArray = $row;
        array_push($resultArray, $tempArray);
    }
    // Finally, encode the array to JSON and output the results
    echo json_encode($resultArray);
}
 
// Close connections
mysqli_close($con);
?>