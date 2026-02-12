<?php
// Start or resume the session so data can be shared across pages
session_start();
require_once("s.Cart.php");

// Give this store a unique ID name
$storeID = "ELECBOUT";

// Check if there’s already a store object saved in the session
if (isset($_SESSION['currentStore'])) {
    $Store = unserialize($_SESSION['currentStore']);
} else {
    $Store = new ShoppingCart();
}

// Set up the store’s database name
$Store->setDatabase("ggg");

// Set the name of the table that holds all product information
$Store->setTableName("inventory");

// Set the store’s unique ID
$Store->setStoreID($storeID);
$storeInfo = $Store->getStoreInformation();

// Handle any actions or input from the user (like adding or removing products)
$Store->processUserInput();

?>
<!DOCTYPE html>
<html>
<head>
  <!-- The page title will display the store name -->
  <title><?php echo $storeInfo['name']; ?></title>
  <link rel="stylesheet" href="php_styles.css">

  <!-- Inline CSS styles for layout and colors -->
  <style>
    body {
     color:#0000FF; 
     font-family:sans-serif; 
     background-color:#F8F8FF;
     font-variant:small-caps; 
    }

    p {
     margin-left:20px;
    }

    h1, h2, th, td {
     color:#000080;
    }

    h1, h2, th {
     text-align:center; 
     font-weight:bold; 
    }

    h1 {
     text-decoration:underline; 
    }

    h2 {
     font-style:italic; 
    }

    table, th, td {
     border-color:#000080;
     border-style:solid;
     border-width:1px;
    }

    table {
     padding:10px; 
     border-width:5px; 
     margin:10px; 
     border-collapse:collapse;
    }

    th {
     background-color:#C0C0FF; 
     border-bottom-width:5px;
    }

    td {
     background-color:#F0F0FF; 
     border-bottom-width:3px;
    }

    .currency {
        text-align:right;
        font-family:monotype;
        font-style:normal; 
    }
  </style>
</head>

<body>
  <!-- Display the store name, description, and welcome message -->
  <h1><?php echo htmlentities($storeInfo['name']); ?></h1>
  <h2><?php echo htmlentities($storeInfo['description']); ?></h2>
  <p><?php echo htmlentities($storeInfo['welcome']); ?></p>

  <?php
    // Show the list of products available in the store
    $Store->getProductList();

    // Create a link that allows the user to view their shopping cart
    echo "<p><a href='showCart.php?PHPSESSID=". session_id()."'>Show Shopping Cart</a></p>\n";

    // Save the current store object back into the session for later use
    $_SESSION['currentStore'] = serialize($Store);
  ?>

  <!-- Buttons for checkout and going back to the main category page -->
  <button><a href="checkout.php">Checkout</a></button>
  <button><a href="GGC.php">Back to Main Categories</a></button>
   
</body>
</html>
