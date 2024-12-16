<?php
session_start();
include 'header.php';  // Including the header
include 'config.php';  // Database connection

// Assuming user_id is stored in session
$user_id = $_SESSION['user_id'] ?? 1;  // Simulated user ID, replace with actual logic
$conn = connectDB();

// Check if there is already a cart for the user with status 'Laukiantis patvirtinimo'
$sql = "SELECT id FROM uzsakymas WHERE fk_Naudotojas = ? AND Statusas = 'Laukiantis patvirtinimo' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// If a cart exists, retrieve the cart ID
if ($result->num_rows > 0) {
    $cart = $result->fetch_assoc();
    $order_id = $cart['id'];
} else {
    // Create a new cart if none exists
    $status = 'Laukiantis patvirtinimo';
    $insert_cart = "INSERT INTO uzsakymas (Data, Statusas, fk_Naudotojas) VALUES (NOW(), ?, ?)";
    $stmt = $conn->prepare($insert_cart);
    $stmt->bind_param("si", $status, $user_id);
    $stmt->execute();
    $order_id = $conn->insert_id;
}

// Handle adding items to the cart
if (isset($_POST['add_to_cart']) && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];

    // Check if the item is already in the cart
    $sql_check = "SELECT * FROM uzsakymo_preke WHERE fk_Uzsakymas = ? AND fk_Preke = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("ii", $order_id, $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the quantity if the item exists
        $stmt_update = "UPDATE uzsakymo_preke SET Kiekis = Kiekis + 1 WHERE fk_Uzsakymas = ? AND fk_Preke = ?";
        $stmt = $conn->prepare($stmt_update);
        $stmt->bind_param("ii", $order_id, $item_id);
        $stmt->execute();
    } else {
        // Add the item to the cart if it doesn't exist
        $stmt_insert = "INSERT INTO uzsakymo_preke (Kiekis, fk_Uzsakymas, fk_Preke) VALUES (1, ?, ?)";
        $stmt = $conn->prepare($stmt_insert);
        $stmt->bind_param("ii", $order_id, $item_id);
        $stmt->execute();
    }

    // Redirect to prevent re-submission on page reload
    header("Location: cart.php");
    exit;
}

// Handle removing items from the cart
if (isset($_GET['remove'])) {
    $remove_item_id = $_GET['remove'];
    $delete_item = "DELETE FROM uzsakymo_preke WHERE fk_Uzsakymas = ? AND fk_Preke = ?";
    $stmt = $conn->prepare($delete_item);
    $stmt->bind_param("ii", $order_id, $remove_item_id);
    $stmt->execute();
}

// Display the cart items
echo "<h1>Jūsų krepšelis</h1>";
echo "<table border='1'>
      <tr><th>Prekė</th><th>Kaina</th><th>Nuolaida</th><th>Kaina po nuolaidos</th><th>Kiekis</th><th>Iš viso</th><th>Veiksmas</th></tr>";

$grand_total = 0;

// Fetch items from the cart
$sql_items = "SELECT p.Pavadinimas, p.Kaina, p.Nuolaida, up.Kiekis, p.id AS product_id 
              FROM uzsakymo_preke up
              JOIN preke p ON up.fk_Preke = p.id
              WHERE up.fk_Uzsakymas = ?";
$stmt = $conn->prepare($sql_items);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

if ($items_result->num_rows > 0) {
    while ($item = $items_result->fetch_assoc()) {
        // Apply discount to the item price
        $discounted_price = $item['Kaina'] - ($item['Kaina'] * $item['Nuolaida'] / 100);
        $discounted_price = number_format($discounted_price, 2);
        $total = $discounted_price * $item['Kiekis'];
        $grand_total += $total;

        echo "<tr>
                <td>{$item['Pavadinimas']}</td>
                <td>{$item['Kaina']} €</td>
                <td>{$item['Nuolaida']}%</td>
                <td>{$discounted_price} €</td>
                <td>{$item['Kiekis']}</td>
                <td>{$total} €</td>
                <td><a href='cart.php?remove={$item['product_id']}'>Pašalinti</a></td>
              </tr>";
    }
    echo "<tr><td colspan='5' style='text-align:right;'>Grand Total:</td><td>{$grand_total} €</td></tr>";
    echo "</table>";
    echo "<p><a href='checkout.php'>Eiti į apmokėjimą</a></p>";
} else {
    echo "<p>Jūsų krepšelis yra tuščias</p>";
}

$conn->close();
?>
