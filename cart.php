<?php
session_start();
include 'header.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to cart
if (isset($_POST['add_to_cart'])) {
    $item_id = $_POST['item_id'];
    $item_name = $_POST['Pavadinimas'];
    $item_price = $_POST['Kaina'];

    if (!isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] = ['name' => $item_name, 'price' => $item_price, 'quantity' => 1];
    } else {
        $_SESSION['cart'][$item_id]['quantity'] += 1;
    }
}

// Handle "Buy Now"
if (isset($_POST['buy_now'])) {
    $_SESSION['cart'] = [
        $_POST['item_id'] => ['name' => $_POST['Pavadinimas'], 'price' => $_POST['Kaina'], 'quantity' => 1]
    ];
    header("Location: checkout.php");
    exit;
}

// Remove item
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
}

// Display cart
echo "<h1>Your Shopping Cart</h1>";
if (!empty($_SESSION['cart'])) {
    echo "<table border='1'>";
    echo "<tr><th>Item</th><th>Price</th><th>Quantity</th><th>Total</th><th>Action</th></tr>";

    $grand_total = 0;
    foreach ($_SESSION['cart'] as $id => $item) {
        $total = $item['price'] * $item['quantity'];
        $grand_total += $total;
        echo "<tr>
                <td>{$item['name']}</td>
                <td>{$item['price']} €</td>
                <td>{$item['quantity']}</td>
                <td>{$total} €</td>
                <td><a href='cart.php?remove={$id}'>Remove</a></td>
              </tr>";
    }
    echo "</table>";
    echo "<h3>Grand Total: {$grand_total} €</h3>";
    echo "<a href='checkout.php'>Proceed to Checkout</a>";
} else {
    echo "<p>Your cart is empty!</p>";
}
?>
