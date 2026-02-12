<?php
// Start the session so we can track the user's activity and data
session_start();

// Include the shopping cart file that has the necessary classes and functions
require_once("s.Cart.php");

// Connect to the database server
$DBConnect = new mysqli("localhost", "root", "", "ggg");

// Check if there is any connection error. If yes, show a message and stop the script
if ($DBConnect->connect_error) {
    die("<p>Database connection failed: " . $DBConnect->connect_error . "</p>");
}

// this helps link orders to a specific user session
$sessionID = session_id();

// Display the page title and short message
echo "<h1>Order History</h1>";
echo "<p>Showing all orders you have placed.</p>";

// Create a SQL query to get all past orders for this user session
$sql = "SELECT orderNumber, totalAmount, orderDate 
        FROM order_summary 
        WHERE sessionID = '$sessionID'
        ORDER BY orderDate DESC";

// Run the SQL query
$result = $DBConnect->query($sql);


if ($result && $result->num_rows > 0) {
    echo "<table border='1' width='100%' cellpadding='5'>";
    echo "<tr><th>Order Number</th><th>Total Amount</th><th>Date</th><th>Actions</th></tr>";

    // Loop through each order and display its details in a table row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['orderNumber']}</td>"; 
        echo "<td>R" . number_format($row['totalAmount'], 2) . "</td>"; 
        echo "<td>{$row['orderDate']}</td>"; 
        
        echo "<td><a href='OrderDetails.php?orderID={$row['orderNumber']}'>View Items</a></td>";
        echo "</tr>";
    }

    echo "</table>";
} else {

    echo "<p>You have not placed any orders yet.</p>";
}

// Add a link to go back to the main shopping categories page
echo "<p><a href='GGC.php'>Back to Main Categories</a></p>";
?>
