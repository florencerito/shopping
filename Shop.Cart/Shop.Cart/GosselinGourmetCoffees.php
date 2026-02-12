<?php
// Start or resume the current session so user data (like the shopping cart) can be saved
session_start();

require_once("s.Cart.php");
$storeID = "COFFEE";

// Check if the ShoppingCart class exists before using it (prevents errors)
if (class_exists("ShoppingCart")) {
    if (isset($_SESSION['currentStore'])) { 
        $Store = unserialize($_SESSION['currentStore']);
    } else {
        $Store = new ShoppingCart();
    }

    // Set up the database name that stores the product information
    $Store->setDatabase("ggg");

    // Set which database table contains the product inventory
    $Store->setTableName("inventory");

    $Store->setStoreID($storeID);

    // Get store information such as name, description, and welcome message
    $storeInfo = $Store->getStoreInformation();
           
    // Process any user input — like adding or removing products from the cart
    $Store->processUserInput();
}
?>

<!DOCTYPE html>
<html>
<head>
  <!-- The page title will display the name of the store -->
  <title><?php echo $storeInfo['name']; ?></title>
  
  <!-- Link to the external stylesheet for general page styling -->
  <link rel="stylesheet" href="php_styles.css">
  
  <!-- Additional CSS styles specific to this page -->
  <style>
    body {
     color:#000020; 
     font-family:serif; 
     background-color:#FFFFFF;
    }

    p {
     margin-left:20px;
    }

    h1, h2, th, td {
     color:#000040;
    }

    h1, h2, th {
     text-align:center; 
     font-weight:bold; 
    }

    h1 {
     text-decoration:underline; 
     font-variant:small-caps; 
    }

    h2 {
     font-style:italic; 
    }

    table, th, td {
     border-color:#008080;
     border-style:solid;
     border-width:1px;
    }

    table {
     padding:2px; 
     border-width:2px; 
     margin:10px; 
     border-collapse:separate;
     border-spacing:3px;
    }

    th {
     padding:10px; 
     background-color:#C0FFFF; 
    }

    td {
     padding:10px; 
     background-color:#E0FFFF; 
    }

    .currency {
        text-align:right;
        font-family:monotype;
        font-style:normal; 
    }
  </style>
</head>

<body class="">
  <!-- Display the store name, description, and welcome message from the database -->
  <h1><?php echo htmlentities($storeInfo['name']); ?></h1>
  <h2><?php echo htmlentities($storeInfo['description']); ?></h2>
  <p><?php echo htmlentities($storeInfo['welcome']); ?></p>

  <?php


    // Show the product list available in this store category
    $Store->getProductList();
    
    // Provide a link to the page where users can view their shopping cart
    echo "<p><a href='showCart.php?PHPSESSID=". session_id()."'>Show Shopping Cart</a></p>\n";

    // Save the updated store object back into the session for later use
    $_SESSION['currentStore'] = serialize($Store);
  ?>
   
   <!-- Buttons for checking out and going back to the main category page -->
   <button><a href="checkout.php">Checkout</a></button>
   <button><a href="GGC.php">Back to Main Categories</a></button>

</body>
</html>
