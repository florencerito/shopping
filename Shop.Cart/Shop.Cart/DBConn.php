<?php
$location="localhost";
$user="root";
$password = "";
$database = "ggg";

$DBConnect = null;
	$ErrorMsgs = array(); 
	$DBConnect = new mysqli($location, $user, $password, $database);
if ($DBConnect->connect_error) {
    $ErrorMsgs[] = "The database server is not available.";
    echo "error in connection";
}
else {
    echo "<p>Connection to Proverb DB is valid</p>";
}
	
?>