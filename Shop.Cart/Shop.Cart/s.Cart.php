<?php
// Define the ShoppingCart class to handle all shopping cart operations
class ShoppingCart {

    private $DBConnect;          
    private $DBName = "ggg";     
    private $TableName = "";
    private $Count = 0;          
    private $storeID = "";
    private $Balance = null;     
    private $Orders = array();
    private $OrderTable = array();
    private $inventory = array();
    private $shoppingCart = array(); 

    // Constructor runs automatically when the class is created
    public function __construct() {
        $this->DBConnect = new mysqli("localhost", "root", "", "ggg");

        if (mysqli_connect_errno()) {
            die("<p>Unable to connect to database.</p>" .
            "<p>Error code " . mysqli_connect_errno() .
            ": " . mysqli_connect_error() . "</p>");
        }

        // If a shopping cart already exists in session, load it
        if (isset($_SESSION['shoppingCart'])) {
            $this->shoppingCart = $_SESSION['shoppingCart'];
        } else {
            $_SESSION['shoppingCart'] = array(); 
            $this->shoppingCart = array();
        }
    }

    // Destructor — closes the database connection when the class is done
    function __destruct() {
        if (!$this->DBConnect->connect_error)
            $this->DBConnect->close();
    }

    // Returns the name of the database
    public function getDatabase() {
        return $this->DBName;
    }

    // Reconnect to database after unserializing the object
    function __wakeup() {
        include("DBConn.php");
        $this->DBConnect = $DBConnect;
    }

    // Define which variables should be saved when object is serialized
    function __sleep() {
        $SerialVars = array('Balance');
        return $SerialVars;
    }

    // Set the database name and connect to it
    public function setDatabase($database) {
        $this->DBName = $database;
        $this->DBConnect->select_db($this->DBName)
            or die("<p>Unable to select the database.</p>"
            . "<p>Error code " . mysqli_errno($this->DBConnect)
            . ": " . mysqli_error($this->DBConnect) . "</p>");
    }

    // Set which table in the database to use
    public function setTableName($Table) {
        $this->TableName = $Table;
    }

    // Set the store ID and load all products from that store
    public function setStoreID($storeID) {
        if ($this->storeID == $storeID) {
            return; 
        }

        $this->storeID = $storeID;

        // Get all products that belong to this store
        $SQLString = "SELECT * FROM " . $this->TableName .
             " WHERE storeID = '" . $this->storeID . "'";
        $QueryResult = @$this->DBConnect->query($SQLString);

        // If query fails, reset everything
        if ($QueryResult === FALSE) {
            $this->inventory = array();
            $this->shoppingCart = array();
            $this->storeID = "";
            return;
        }

        // Fill the inventory array with products
        $this->inventory = array();
        while (($Row = $QueryResult->fetch_assoc()) !== NULL) {
            $id = $Row['productID'];
            $this->inventory[$id] = [
                'name' => $Row['name'],
                'description' => $Row['description'],
                'price' => $Row['price']
            ];

            // Make sure each product exists in the cart with quantity 0
            if (!isset($this->shoppingCart[$id])) {
                $this->shoppingCart[$id] = 0;
            }
        }
    }

    // Get store information such as name, description, and welcome text
    public function getStoreInformation() {
        $retval = FALSE;
        if ($this->storeID != "") {
            $SQLString = "SELECT * FROM store_info WHERE storeID = '" . $this->DBConnect->real_escape_string($this->storeID) . "'";
            $QueryResult = @$this->DBConnect->query($SQLString);
            if ($QueryResult !== FALSE) {
                $retval = $QueryResult->fetch_assoc();
            }
        }
        return $retval;
    }

    // Show all available products and display them in a table
    public function getProductList() {
        $retval = FALSE;
        $subtotal = 0.0;

        // If no products are loaded yet, fetch from the database
        if (count($this->inventory) === 0 && $this->TableName != "") {
            $sql = "SELECT * FROM " . $this->DBConnect->real_escape_string($this->TableName);
            $res = $this->DBConnect->query($sql);
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $id = $row['productID'];
                    $this->inventory[$id] = array(
                        'name' => $row['name'],
                        'description' => $row['description'],
                        'price' => (float)$row['price']
                    );
                    $this->shoppingCart[$id] = isset($this->Orders[$id]) ? (int)$this->Orders[$id] : 0;
                    if (!isset($this->OrderTable[$id])) $this->OrderTable[$id] = $this->TableName;
                }
            }
        }

        // If still no products, show message
        if (count($this->inventory) === 0) {
            echo "<p>No products available.</p>";
            return false;
        }

        // Display products in a table
        echo "<table width='100%' border='1' cellspacing='0' cellpadding='5'>\n";
        echo "<tr><th>Product</th><th>Description</th><th>Price Each</th><th># in Cart</th><th>Total Price</th><th>Actions</th></tr>\n";

        foreach ($this->inventory as $ID => $Info) {
            $qtyInCart = isset($this->shoppingCart[$ID]) ? (int)$this->shoppingCart[$ID] : 0;
            echo "<tr>";
            echo "<td>" . htmlentities($Info['name']) . "</td>\n";
            echo "<td>" . htmlentities($Info['description']) . "</td>\n";
            printf("<td class='currency'>$%.2f</td>\n", $Info['price']);
            echo "<td class='currency'>" . $qtyInCart . "</td>\n";
            printf("<td class='currency'>$%.2f</td>\n", $Info['price'] * $qtyInCart);

            // Add or remove items using links
            $base = htmlspecialchars($_SERVER['SCRIPT_NAME']);
            echo "<td>
                <a href='{$base}?PHPSESSID=" . session_id() . "&ItemToAdd={$ID}'>Add</a> |
                <a href='{$base}?PHPSESSID=" . session_id() . "&ItemToRemove={$ID}'>Remove</a>
                </td>\n";

            $subtotal += ($Info['price'] * $qtyInCart);
            echo "</tr>\n";
        }

        // Show subtotal and option to empty cart
        echo "<tr><td colspan='4'><b>Subtotal</b></td>\n";
        printf("<td class='currency'><b>$%.2f</b></td>\n", $subtotal);
        echo "<td><a href='" . $_SERVER['SCRIPT_NAME'] . "?PHPSESSID=" . session_id() . "&EmptyCart=TRUE'>Empty Cart</a></td></tr>\n";
        echo "</table>";

        $retval = TRUE;
        return ($retval);
    }

    // Add an item to the cart
    public function addItem() {
        if (!isset($_GET['ItemToAdd'])) return;

        $ProdID = $_GET['ItemToAdd'];

        // Load current cart
        if (isset($_SESSION['shoppingCart'])) {
            $this->shoppingCart = $_SESSION['shoppingCart'];
        }

        // Increase quantity or add new item
        if (array_key_exists($ProdID, $this->shoppingCart)) {
            $this->shoppingCart[$ProdID]++;
        } else {
            $this->shoppingCart[$ProdID] = 1;
        }

        // Save back to session
        $_SESSION['shoppingCart'] = $this->shoppingCart;
    }

    // Process user actions — add, remove, or empty cart
    public function processUserInput() {
        if (!empty($_GET['ItemToAdd']))
            $this->addItem();
        if (!empty($_GET['ItemToRemove']))
            $this->removeItem();
        if (!empty($_GET['EmptyCart']))
            $this->emptyCart();
    }

    // Remove one quantity of an item from the cart
    private function removeItem() {
        if (!isset($_GET['ItemToRemove'])) return;

        $ProdID = $_GET['ItemToRemove'];

        if (isset($_SESSION['shoppingCart'])) {
            $this->shoppingCart = $_SESSION['shoppingCart'];
        }

        if (isset($this->shoppingCart[$ProdID]) && $this->shoppingCart[$ProdID] > 0) {
            $this->shoppingCart[$ProdID]--;
        }

        $_SESSION['shoppingCart'] = $this->shoppingCart;
    }

    // Empty the entire cart
    private function emptyCart() {
        foreach ($this->shoppingCart as $key => $value)
            $this->shoppingCart[$key] = 0;
    }

    // Show all items currently in the cart
    public function showCart() {
        if (isset($_SESSION['shoppingCart'])) {
            $this->shoppingCart = $_SESSION['shoppingCart'];
        }

        if (empty($this->shoppingCart)) {
            echo "<p>Your shopping cart is empty.</p>";
            return;
        }

        echo "<table border='1' width='100%' cellpadding='5'>";
        echo "<tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr>";
        $total = 0;

        // Loop through cart items and show them
        foreach ($this->shoppingCart as $id => $qty) {
            if ($qty > 0) {
                $sql = "SELECT * FROM inventory WHERE productID='$id'";
                $res = $this->DBConnect->query($sql);
                if ($res && $res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $lineTotal = $row['price'] * $qty;
                    $total += $lineTotal;
                    echo "<tr>
                            <td>{$row['name']}</td>
                            <td align='center'>$qty</td>
                            <td align='right'>R" . number_format($row['price'], 2) . "</td>
                            <td align='right'>R" . number_format($lineTotal, 2) . "</td>
                          </tr>";
                }
            }
        }

        echo "<tr><td colspan='3' align='right'><b>Total</b></td>
              <td align='right'><b>R" . number_format($total, 2) . "</b></td></tr>";
        echo "</table>";
    }

    // Display cart summary and totals
    public function getsCart() {
        $retval = FALSE;
        $subtotal = 0.0;
        $totalItems = 0;

        // Load products if not already in memory
        if (count($this->inventory) === 0 && $this->TableName != "") {
            $sql = "SELECT * FROM `" . $this->DBConnect->real_escape_string($this->TableName) . "`";
            $res = $this->DBConnect->query($sql);
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $id = $row['productID'];
                    $this->inventory[$id] = array(
                        'name' => $row['name'],
                        'description' => $row['description'],
                        'price' => (float)$row['price']
                    );
                    $this->shoppingCart[$id] = isset($this->Orders[$id]) ? (int)$this->Orders[$id] : 0;
                    if (!isset($this->OrderTable[$id])) $this->OrderTable[$id] = $this->TableName;
                }
            }
        }

        if (count($this->inventory) === 0) {
            echo "<p>No products available.</p>";
            return false;
        }

        // Count total items and calculate total cost
        foreach ($this->shoppingCart as $id => $qty) {
            $totalItems += $qty;
            if (isset($this->inventory[$id])) {
                $subtotal += $this->inventory[$id]['price'] * $qty;
            }
        }

        // Display summary
        echo "<h2> You have <strong>{$totalItems}</strong> item(s) in your cart. 
              Total: <strong>R" . number_format($subtotal, 2) . "</strong></h2>";

        // Display all items in table form
        echo "<table width='100%' border='1' cellspacing='0' cellpadding='5'>\n";
        echo "<tr><th>Product</th><th>Description</th><th>Price Each</th><th># in Cart</th><th>Total Price</th><th>Actions</th></tr>\n";

        foreach ($this->inventory as $ID => $Info) {
            $qtyInCart = isset($this->shoppingCart[$ID]) ? (int)$this->shoppingCart[$ID] : 0;
            $lineTotal = $Info['price'] * $qtyInCart;

            echo "<tr>";
            echo "<td>" . htmlentities($Info['name']) . "</td>\n";
            echo "<td>" . htmlentities($Info['description']) . "</td>\n";
            printf("<td class='currency'>R%.2f</td>\n", $Info['price']);
            echo "<td class='currency'>" . $qtyInCart . "</td>\n";
            printf("<td class='currency'>R%.2f</td>\n", $lineTotal);

            $base = htmlspecialchars($_SERVER['SCRIPT_NAME']);
            echo "<td>
                    <a href='{$base}?PHPSESSID=" . session_id() . "&ItemToAdd={$ID}'>Add</a> |
                    <a href='{$base}?PHPSESSID=" . session_id() . "&ItemToRemove={$ID}'>Remove</a>
                  </td>\n";
            echo "</tr>\n";
        }

        echo "<tr><td colspan='4'><b>Subtotal</b></td>\n";
        printf("<td class='currency'><b>R%.2f</b></td>\n", $subtotal);
        echo "<td><a href='" . $_SERVER['SCRIPT_NAME'] . "?PHPSESSID=" . session_id() . "&EmptyCart=TRUE'>Empty Cart</a></td></tr>\n";
        echo "</table>";

        $retval = TRUE;
        return ($retval);
    }

    // Handles the checkout process and saves order to the database
    public function checkout() {
        if (isset($_SESSION['shoppingCart'])) {
            $this->shoppingCart = $_SESSION['shoppingCart'];
        }

        $sessionID = session_id();
        $totalAmount = 0;
        $itemsOrdered = 0;

        // Calculate total price of all items in the cart
        foreach($this->shoppingCart as $productID => $quantity) {
            if ($quantity > 0) {
                $sql = "SELECT price FROM inventory WHERE productID='$productID'";
                $result = $this->DBConnect->query($sql);

                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $price = $row['price'];
                    $totalAmount += $price * $quantity;
                    $itemsOrdered++;
                }
            }
        }

        // If no items in cart, show message
        if ($itemsOrdered == 0) {
            echo "<p>No items in cart to checkout.</p>";
            return;
        }

        // Save order summary (total cost, session ID)
        $summarySQL = "INSERT INTO order_summary (sessionID, totalAmount)
                       VALUES ('$sessionID', '$totalAmount')";
        $this->DBConnect->query($summarySQL);
        $orderNumber = $this->DBConnect->insert_id; // Get order ID from database

        // Save each product ordered
        foreach($this->shoppingCart as $productID => $quantity) {
            if ($quantity > 0) {
                $insertSQL = "INSERT INTO orders (orderID, productID, quantity, orderDate)
                              VALUES ('$orderNumber', '$productID', $quantity, NOW())";
                $this->DBConnect->query($insertSQL);
            }
        }

        // Clear the cart after checkout
        $this->emptyCart();
        $_SESSION['shoppingCart'] = $this->shoppingCart;

        // Show success message and order info
        echo "<h2>Order Successful!</h2>";
        echo "<p><strong>Order Number:</strong> $orderNumber</p>";
        echo "<p><strong>Total Amount:</strong> R" . number_format($totalAmount, 2) . "</p>";
        echo "<p><a href='Orders.php'>View All My Orders</a></p>";
    }
}
?>
