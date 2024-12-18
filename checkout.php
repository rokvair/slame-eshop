<?php
session_start();
include 'config.php';
$conn = connectDB();
include 'header.php';
require('fpdf/fpdf.php');

// Ensure the user is logged in and has an active cart
$user_id = $_SESSION['user_id'] ?? 1;
$user_email = "";

// Connect to the database

// Fetch the email for the logged-in user
$sql_email = "SELECT El_pastas FROM naudotojas WHERE id = ?";
$stmt_email = $conn->prepare($sql_email);
$stmt_email->bind_param("i", $user_id);
$stmt_email->execute();
$result_email = $stmt_email->get_result();

if ($result_email->num_rows > 0) {
    $row_email = $result_email->fetch_assoc();
    $user_email = $row_email['El_pastas'];
} else {
    echo "User email not found!";
    exit;
}

// Fetch the address for the logged-in user
$sql_address = "SELECT Miestas, Gatve, Namo_nr, Buto_nr, Pasto_kodas FROM Adresas WHERE fk_Naudotojas = ?";
$stmt_address = $conn->prepare($sql_address);
$stmt_address->bind_param("i", $user_id);
$stmt_address->execute();
$result_address = $stmt_address->get_result();
$user_address = "";

if ($result_address->num_rows > 0) {
    $row_address = $result_address->fetch_assoc();
    $user_address = $row_address['Gatve'] . " " . $row_address['Namo_nr'];
    if (!empty($row_address['Buto_nr'])) {
        $user_address .= "/" . $row_address['Buto_nr'];
    }
    $user_address .= ", " . $row_address['Miestas'] . ", " . $row_address['Pasto_kodas'];
} else {
    echo "Address not found!";
    exit;
}

// Check for the active cart in 'uzsakymas' table
$sql = "SELECT * FROM uzsakymas WHERE fk_Naudotojas = ? AND Statusas = 'Laukiantis patvirtinimo'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

// If no active cart exists, create a new cart (order) in the 'uzsakymas' table
if ($order_result->num_rows == 0) {
    $status = 'Laukiantis patvirtinimo';
    $insert_order = "INSERT INTO uzsakymas (Data, Statusas, fk_Naudotojas) VALUES (NOW(), ?, ?)";
    $stmt = $conn->prepare($insert_order);
    $stmt->bind_param("si", $status, $user_id);
    $stmt->execute();
    $order_id = $conn->insert_id;
} else {
    $order = $order_result->fetch_assoc();
    $order_id = $order['id'];
}

// Fetch the cart items
$sql_items = "SELECT up.fk_Preke, up.Kiekis, p.Pavadinimas, p.Kaina
              FROM uzsakymo_preke up
              JOIN preke p ON up.fk_Preke = p.id
              WHERE up.fk_Uzsakymas = ?";
$stmt = $conn->prepare($sql_items);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$cart_items = $stmt->get_result();

if ($cart_items->num_rows == 0) {
    echo "<p>Your cart is empty! <a href='index.php'>Shop now</a></p>";
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    $status = 'Patvirtintas';
    $update_order_status = "UPDATE uzsakymas SET Statusas = ? WHERE id = ?";
    $stmt = $conn->prepare($update_order_status);
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();

    while ($item = $cart_items->fetch_assoc()) {
        $new_stock = $item['Kiekis']; // Quantity bought
        $product_id = $item['fk_Preke'];

        // Update the stock quantity in the 'preke' table
        $update_stock_sql = "UPDATE preke SET Kiekis_sandelyje = Kiekis_sandelyje - ? WHERE id = ?";
        $stmt_stock = $conn->prepare($update_stock_sql);
        $stmt_stock->bind_param("ii", $new_stock, $product_id);
        $stmt_stock->execute();

        // Check if the stock update was successful
        if ($stmt_stock->affected_rows == 0) {
            throw new Exception("Failed to update stock for product ID $product_id");
        }
    }

    $conn->commit();

    // Generate PDF receipt
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetTextColor(0, 0, 0);

    $pdf->Cell(0, 10, "Pirkimo SF", 0, 1, 'C');
    $pdf->Cell(0, 10, "Pirkimo nr.: $order_id", 0, 1);
    $pdf->Cell(0, 10, "Data: " . date("Y-m-d H:i:s"), 0, 1);
    $pdf->Cell(0, 10, "Klientas: $user_email", 0, 1); // Display user email
    $pdf->Cell(0, 10, "Adresas: $user_address", 0, 1); // Display user address

    $pdf->Ln(5);
    $pdf->Cell(40, 10, 'Pavadinimas', 1);
    $pdf->Cell(40, 10, 'Kiekis', 1);
    $pdf->Cell(40, 10, 'Kaina', 1);
    $pdf->Cell(40, 10, 'Bendrai', 1);
    $pdf->Ln();

    $total_amount = 0;

    $stmt = $conn->prepare($sql_items);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items_result = $stmt->get_result();

    while ($item = $items_result->fetch_assoc()) {
        $total_item_price = $item['Kaina'] * $item['Kiekis'];
        $total_amount += $total_item_price;

        $pdf->Cell(40, 10, $item['Pavadinimas'], 1);
        $pdf->Cell(40, 10, $item['Kiekis'], 1);
        $pdf->Cell(40, 10, $item['Kaina'] . " eur", 1);
        $pdf->Cell(40, 10, $total_item_price . " eur", 1);
        $pdf->Ln();
    }

    $pdf->Ln(5);
    $pdf->Cell(120, 10, 'Bendra suma:', 1);
    $pdf->Cell(40, 10, $total_amount . ' eur', 1);

    $pdf->Output('F', 'receipts/order_' . $order_id . '.pdf');

    echo "<h1>Valio!</h1>";
    echo "<p>Ačiū, kad apsipirkote. Jūsų užsakymo numeris yra {$order_id}. Sąskaita sukurta: 
          <a href='receipts/order_{$order_id}.pdf' target='_blank'>Sąskaita</a>.</p>";
} catch (Exception $e) {
    $conn->rollback();
    echo "<p>Nepavyko :( {$e->getMessage()}</p>";
}

$conn->close();
?>
