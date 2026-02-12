<?php

// Start or continue the session so the website can keep track of user data
session_start();
require_once("s.Cart.php");

// Create an array that stores general information about the store
$storeInfo = [
    'css_file' => 'php_styles.css',                    
    'name' => 'Gosselin Gourmet Goods',
    'description' => 'Welcome to our online shop!',    
    'welcome' => 'Browse our categories and find amazing products.' 
];

// Check if the ShoppingCart class exists (to prevent errors if the file is missing)
if (class_exists("ShoppingCart")) {
    if (isset($_SESSION['currentStore'])) {
        // Get (unserialize) the store object from the session
        $Store = unserialize($_SESSION['currentStore']);
        
        // If the object found is not a valid ShoppingCart, create a new one
        if (!$Store instanceof ShoppingCart) {
            $Store = new ShoppingCart();
        }
    } else {
        $Store = new ShoppingCart();
    }
} else {
    die("<p style='color:red;'>ShoppingCart class not found! Check your s.Cart.php include path.</p>");
}

?>

<!DOCTYPE html>
<html>
<head>
  <!-- The title of the page will show the store name -->
  <title><?php echo $storeInfo['name']; ?></title>
  <meta charset="iso-8859-1" />
  
  <!-- Link to the external CSS stylesheet -->
  <link rel="stylesheet" href="php_styles.css" type="text/css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $storeInfo['css_file']; ?>" />
</head>
<body>
  
  <?php
  // Connect the store to the correct database
  $Store->setDatabase("ggg");

  // Set which database table to use for products
  $Store->setTableName("inventory");

  // Set the store ID (these could represent different categories or branches)
  $Store->setStoreID("COFFEE");   
  $Store->setStoreID("ELECBOUT");  
  $Store->setStoreID("ANTIQUE");   


  $Store->processUserInput();
  ?>

<!-- Display the store name, description, and welcome message -->
<h1><?php echo htmlentities($storeInfo['name']); ?></h1>
<h2><?php echo htmlentities($storeInfo['description']);?></h2>
<h3>Shop by Category</h3>
<p><?php echo htmlentities($storeInfo['welcome']); ?></p>

  <?php
  // Show the product list for the current store
  $Store->getProductList();

  // Save the store object into the session so the cart data is not lost
  $_SESSION['currentStore'] = serialize($Store);
  ?>

<!-- Links to other category pages -->
<p><a href="GosselinGourmetCoffees.php">Gourmet Coffees</a></p>
<p><a href="ElectronicsBoutique.php">Electronics Boutique</a></p>
<p><a href="OldTymeAntiques.php">Antiques</a></p><br>

<!-- Link to go to the checkout page -->
<p><a href="checkout.php">Checkout</a></p>

<!-- Link to view previous customer orders -->
<p><a href="Orders.php">View My Orders</a></p>

</body>
</html>
