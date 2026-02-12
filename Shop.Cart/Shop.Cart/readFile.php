<?php
$Proverbs = file("proverbs.txt");
echo "<pre>";
echo count($Proverbs);

print_r($Proverbs);
echo "</pre>";

foreach ($Proverbs as $ind => $ProvValue) {
    echo "<p>$ind - $ProvValue</p>";
}

// Include database connection
include("DBConn.php");

// Drop table if it exists
$sqlDropT = "DROP TABLE IF EXISTS Proverb";
$QResultDT = mysqli_query($this->DBConnect, $sqlDropT);
if ($QResultDT === FALSE)
    echo "<p>No Way Hose - Unable to perform SQL Drop Table</p>";
else
    echo "<p>Well done my mate, dropped table successfully.</p>";

// Create table Proverbs
$sqlCreateT = "CREATE TABLE Proverb (
    Pov_ID SMALLINT(6) PRIMARY KEY AUTO_INCREMENT,
    Prov_Name VARCHAR(120)
)";
$QResultCT = mysqli_query($this->DBConnect, $sqlCreateT);
if ($QResultCT === FALSE)
    echo "<p>No Way Hose - Unable to perform SQL Create Table</p>";
else
    echo "<p>Table created successfully: " . mysqli_info($this->DBConnect) . "</p>";

// Insert proverbs into table
echo "<h1>INSERT VALUES INTO TABLE</h1>";
foreach ($Proverbs as $ind => $ProvValue) {
    $ProvValue = addslashes($ProvValue);
    echo "<p>$ProvValue</p>";
    $sqlInsP = "INSERT INTO Proverb (Prov_Name) VALUES ('$ProvValue')";

    $QResultDT = mysqli_query($DBConnect, $sqlDropT);
    if ($QresultA === FALSE)
        echo "<p>No Way Hose - Unable to perform SQL Insert</p>";
    else
        echo "<p>Well done my mate - inserted successfully.</p>";
}
?>
