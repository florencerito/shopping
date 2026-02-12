<?php
// This allows data to be saved between pages
session_start();
require_once("s.Cart.php");

// Create an array with basic information about the store
$storeInfo = array(
    'css_file' => 'php_styles.css',          
    'name' => 'Gosselin Gourmet Goods',      
    'description' => 'Checkout Page',        
);

// Check if there’s already a store object saved in the current session
if (isset($_SESSION['currentStore'])) {
    $Store = unserialize($_SESSION['currentStore']);
} else {
    echo "<p>No store session found. Please add items before checkout.</p>";
    exit;
}

// Use the session ID as a unique order ID
$orderID = session_id();

// Set the database name where the store data is saved
$Store->setDatabase("ggg");

// Set the table name in the database that holds the inventory data
$Store->setTableName("inventory");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <!-- Link the external CSS file for styling -->
  <link rel="stylesheet" href="php_styles.css">
</head>
<body>
  <h1>Checkout</h1>
  <?php
  
  $Store->checkout();

  // Save the updated store object back into the session after checkout
  $_SESSION['currentStore'] = serialize($Store);
  ?>
  
  <!-- Provide a link back to the main categories page -->
  <p><a href="GGC.php">Back to Main Categories</a></p>

</body>
</html>
