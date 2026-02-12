<?php
// Start the session to access saved shopping cart data
session_start();
require_once("s.Cart.php");


// Check if there’s an active shopping cart stored in the session
if (!isset($_SESSION['currentStore'])) {
    echo "<p>Your shopping cart is empty. Go back and add some items!</p>";
    echo "<p><a href='GGC.php'>Back to Categories</a></p>";
    exit; 
}


// If the cart exists, get it from the session and turn it back into an object
$Store = unserialize($_SESSION['currentStore']);


if (is_object($Store) && method_exists($Store, 'processUserInput')) {
    $Store->processUserInput(); 
}

// Display a heading for the cart page
echo "<h1>Your Shopping Cart</h1>";

// Check again if the cart object exists and has the showCart() method
if (is_object($Store) && method_exists($Store, 'showCart')) {
    $Store->showCart(); 
} else {
    echo "<p>Unable to load shopping cart. Please return to categories.</p>";
}

// Save the updated cart back into the session
$_SESSION['currentStore'] = serialize($Store);


// Provide navigation links for the user to continue shopping or checkout
echo "<p><a href='Orders.php'>View My Orders</a></p>";
echo "<p><a href='checkout.php'>Checkout</a></p>";
echo "<p><a href='GGC.php'>Back to Categories</a></p>";

?>
