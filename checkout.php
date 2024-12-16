<?php
session_start();
include 'config.php';
include 'header.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty! <a href='index.php'>Shop now</a></p>";
    exit;
}

// Simulated user ID (replace with real session logic)
$user_id = $_SESSION['user_id'] ?? 1;

$conn = connectDB();
$conn->begin_transaction();

try {
    // Insert into 'uzsakymas' table
    $status = 'Patvirtintas';
    $insert_order = "INSERT INTO uzsakymas (Data, Statusas, fk_Naudotojas) VALUES (NOW(), ?, ?)";
    $stmt = $conn->prepare($insert_order);
    $stmt->bind_param("si", $status, $user_id);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Insert items into 'uzsakymo_preke' table
    foreach ($_SESSION['cart'] as $id => $item) {
        $insert_item = "INSERT INTO uzsakymo_preke (Kiekis, fk_Uzsakymas, fk_Preke) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_item);
        $stmt->bind_param("iii", $item['quantity'], $order_id, $id);
        $stmt->execute();
    }

    $conn->commit();
    unset($_SESSION['cart']); // Clear the cart
    echo "<h1>Order Successful!</h1>";
    echo "<p>Thank you for your purchase. Your order ID is {$order_id}.</p>";
} catch (Exception $e) {
    $conn->rollback();
    echo "<p>Order failed: {$e->getMessage()}</p>";
}

$conn->close();
?>
