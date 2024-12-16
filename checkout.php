<?php
session_start();
include 'config.php';
include 'header.php';
require('fpdf/fpdf.php');

// Ensure the user is logged in and has an active cart
$user_id = $_SESSION['user_id'] ?? 1;

// Check for the active cart in 'uzsakymas' table
$conn = connectDB();

// Get the user's active cart order, where status is 'Laukiantis patvirtinimo'
$sql = "SELECT * FROM uzsakymas WHERE fk_Naudotojas = ? AND Statusas = 'Laukiantis patvirtinimo'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

// If no active cart exists, create a new cart (order) in the 'uzsakymas' table
if ($order_result->num_rows == 0) {
    // Create a new cart (order) in 'uzsakymas' table with status 'Laukiantis patvirtinimo'
    $status = 'Laukiantis patvirtinimo'; // Pending confirmation
    $insert_order = "INSERT INTO uzsakymas (Data, Statusas, fk_Naudotojas) VALUES (NOW(), ?, ?)";
    $stmt = $conn->prepare($insert_order);
    $stmt->bind_param("si", $status, $user_id);
    $stmt->execute();
    $order_id = $conn->insert_id;
} else {
    // Use the existing cart order
    $order = $order_result->fetch_assoc();
    $order_id = $order['id'];
}

// Fetch the cart items from 'uzsakymo_preke'
$sql_items = "SELECT up.fk_Preke, up.Kiekis, p.Pavadinimas, p.Kaina
              FROM uzsakymo_preke up
              JOIN preke p ON up.fk_Preke = p.id
              WHERE up.fk_Uzsakymas = ?";
$stmt = $conn->prepare($sql_items);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$cart_items = $stmt->get_result();

// If the cart is empty, show a message
if ($cart_items->num_rows == 0) {
    echo "<p>Your cart is empty! <a href='index.php'>Shop now</a></p>";
    exit;
}

// Begin transaction to confirm the order
$conn->begin_transaction();

try {
    // Update the order status to 'Patvirtintas' (Confirmed)
    $status = 'Patvirtintas';
    $update_order_status = "UPDATE uzsakymas SET Statusas = ? WHERE id = ?";
    $stmt = $conn->prepare($update_order_status);
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();

    // Commit the transaction to finalize the order
    $conn->commit();

    // Generate PDF receipt
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "Order Receipt", 0, 1, 'C');
    $pdf->Cell(0, 10, "Order ID: $order_id", 0, 1);
    $pdf->Cell(0, 10, "Date: " . date("Y-m-d H:i:s"), 0, 1);
    $pdf->Cell(0, 10, "User ID: $user_id", 0, 1);

    // List the items in the order
    $pdf->Ln(5);
    $pdf->Cell(40, 10, 'Item Name', 1);
    $pdf->Cell(40, 10, 'Quantity', 1);
    $pdf->Cell(40, 10, 'Price', 1);
    $pdf->Cell(40, 10, 'Total', 1);
    $pdf->Ln();

    $total_amount = 0;

    // Fetch the items for this order and generate the PDF content
    $sql_items = "SELECT p.Pavadinimas, up.Kiekis, p.Kaina
                  FROM uzsakymo_preke up
                  JOIN preke p ON up.fk_Preke = p.id
                  WHERE up.fk_Uzsakymas = ?";
    $stmt = $conn->prepare($sql_items);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items_result = $stmt->get_result();

    while ($item = $items_result->fetch_assoc()) {
        $total_item_price = $item['Kaina'] * $item['Kiekis'];
        $total_amount += $total_item_price;

        $pdf->Cell(40, 10, $item['Pavadinimas'], 1);
        $pdf->Cell(40, 10, $item['Kiekis'], 1);
        $pdf->Cell(40, 10, $item['Kaina'] . " €", 1);
        $pdf->Cell(40, 10, $total_item_price . " €", 1);
        $pdf->Ln();
    }

    // Add the total amount
    $pdf->Ln(5);
    $pdf->Cell(120, 10, 'Total Amount:', 1);
    $pdf->Cell(40, 10, $total_amount . ' €', 1);

    // Output the PDF
    $pdf->Output('F', 'receipts/order_' . $order_id . '.pdf');

    // Confirmation message
    echo "<h1>Order Successful!</h1>";
    echo "<p>Thank you for your purchase. Your order ID is {$order_id}. A receipt has been generated. <a href='receipts/order_{$order_id}.pdf' target='_blank'>Download Receipt</a>.</p>";
} catch (Exception $e) {
    // If any error occurs, rollback the transaction
    $conn->rollback();
    echo "<p>Order failed: {$e->getMessage()}</p>";
}

$conn->close();
?>
