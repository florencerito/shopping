<?php
// Start the session so data can be shared between pages
session_start();

// Include the shopping cart class file
require_once("s.Cart.php");

// Connect to the database (localhost server, username 'root', no password, database name 'ggg')
$DBConnect = new mysqli("localhost", "root", "", "ggg");

// Check if the connection failed, and stop the program if it did
if ($DBConnect->connect_error) {
    die("<p>Database connection failed: " . $DBConnect->connect_error . "</p>");
}

// Check if an order ID was passed in the URL
if (!isset($_GET['orderID'])) {
    echo "<p>Invalid order number.</p>";
    exit;
}

// Get the order ID from the URL and make sure it’s a number (for security)
$orderID = intval($_GET['orderID']);

// Create a SQL query to get all items in the order
$sql = "SELECT o.productID, o.quantity, i.name, i.price
        FROM orders AS o
        JOIN inventory AS i ON o.productID = i.productID
        WHERE o.orderID = $orderID";

// Run the query on the database
$result = $DBConnect->query($sql);

// Show the order number as a heading
echo "<h1>Order #$orderID Details</h1>";

// If the order has items, display them in a table
if ($result && $result->num_rows > 0) {
    echo "<table border='1' width='100%' cellpadding='5'>";
    echo "<tr><th>Product</th><th>Quantity</th><th>Price Each</th><th>Total</th></tr>";

    $grandTotal = 0; 

    // Loop through each product in the order
    while ($row = $result->fetch_assoc()) {
        $lineTotal = $row['price'] * $row['quantity'];
        $grandTotal += $lineTotal; 

        // Display each item’s details in a row
        echo "<tr>";
        echo "<td>{$row['name']}</td>"; 
        echo "<td align='center'>{$row['quantity']}</td>"; 
        echo "<td align='right'>R" . number_format($row['price'], 2) . "</td>"; 
        echo "<td align='right'>R" . number_format($lineTotal, 2) . "</td>"; 
        echo "</tr>";
    }

    // Display the grand total at the bottom of the table
    echo "<tr><td colspan='3' align='right'><b>Total</b></td><td align='right'><b>R" . number_format($grandTotal, 2) . "</b></td></tr>";
    echo "</table>";
} else {
    // If no products were found for that order
    echo "<p>No items found for this order.</p>";
}

// Add a link to go back to the Order History page
echo "<p><a href='Orders.php'>Back to Order History</a></p>";
?>
