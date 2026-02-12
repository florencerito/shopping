<?php
// Start the session to store and access user data between pages
session_start();
require_once("s.Cart.php");
$storeID = "ANTIQUE";

// Check if there is an existing store session
if (isset($_SESSION['currentStore'])) {
    $Store = unserialize($_SESSION['currentStore']);
} else {
    $Store = new ShoppingCart();
}

// Set up database connection details for the store
$Store->setDatabase("ggg");          
$Store->setTableName("inventory");    
$Store->setStoreID($storeID);         
$storeInfo = $Store->getStoreInformation(); 

// Process any user actions like adding items to cart or updating quantities
$Store->processUserInput();

?>
<!DOCTYPE html>
<html>
<head>
  <!-- Set the page title to the store’s name -->
  <title><?php echo $storeInfo['name']; ?></title>
  
  <!-- Link external CSS file for styling -->
  <link rel="stylesheet" href="php_styles.css">

  <!-- Additional custom styling directly inside the page -->
  <style>
    body {
     color:#403010; 
     font-family:"Lucida Calligraphy", "Brush Script MT", "English 157 BT", cursive; 
     background-color:#FFF0D0;
     font-style:italic; 
}

p {
     margin-left:20px;
}

h1, h2, th, td {
     color:#403010;
}

h1, h2, th {
     text-align:center; 
     font-weight:bold; 
}

table, th, td {
     border-style:none;
}

table {
     padding:10px; 
     margin:10px; 
     vertical-align:top;
}

td {
     vertical-align:top;
}

.currency {
    text-align:right;
    font-family:monotype;
    font-style:normal; 
}

  </style>
</head>
<body class="">
<!-- Display store name, description, and welcome message -->
<h1><?php echo htmlentities($storeInfo['name']); ?></h1>
<h2><?php echo htmlentities($storeInfo['description']); ?></h2>
<p><?php echo htmlentities($storeInfo['welcome']); ?></p>

  <?php
    // Show list of products available in the store
    $Store->getProductList();
    
    // Provide a link to view the shopping cart
    echo "<p><a href='showCart.php?PHPSESSID=". session_id()."'>Show Shopping Cart</a></p>\n";
    
    // Save the current store state in the session for next page load
    $_SESSION['currentStore'] = serialize($Store);
  ?>

   <!-- Buttons to go to checkout or back to main category page -->
   <button><a href="checkout.php">Checkout</a></button>
   <button><a href="GGC.php">Back to Main Categories</a></button>
</body>
</html>
