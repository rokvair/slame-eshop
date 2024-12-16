<?php
session_start();
include 'config.php';  // Database connection
$conn = connectDB();
include 'header.php';  // Including the header

// Check if order_id is provided in the query string
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    echo "<p>Nenurodytas užsakymo ID.</p>";
    exit;
}

$order_id = intval($_GET['order_id']);

// Verify that the order belongs to the logged-in user
$user_id = $_SESSION['user_id'] ?? 1;  // Replace with actual user authentication
$sql_verify = "SELECT * FROM uzsakymas WHERE id = ? AND fk_Naudotojas = ?";
$stmt = $conn->prepare($sql_verify);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Užsakymas nerastas arba nepriklauso jums.</p>";
    exit;
}

// Fetch order details
$order = $result->fetch_assoc();
$order_date = $order['Data'];
$order_status = $order['Statusas'];

echo "<h1>Užsakymo detalės (ID: {$order_id})</h1>";
echo "<p>Data: {$order_date}</p>";
echo "<p>Statusas: {$order_status}</p>";

// Fetch items in the order
$sql_items = "SELECT p.Pavadinimas, p.Kaina, p.Nuolaida, up.Kiekis, 
                     (p.Kaina - (p.Kaina * p.Nuolaida / 100)) AS DiscountedPrice
              FROM uzsakymo_preke up
              JOIN preke p ON up.fk_Preke = p.id
              WHERE up.fk_Uzsakymas = ?";
$stmt = $conn->prepare($sql_items);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

// Display order items
if ($items_result->num_rows > 0) {
    echo "<table border='1'>
          <tr>
            <th>Prekė</th>
            <th>Kaina</th>
            <th>Nuolaida</th>
            <th>Kaina po nuolaidos</th>
            <th>Kiekis</th>
            <th>Iš viso</th>
          </tr>";

    $grand_total = 0;

    while ($item = $items_result->fetch_assoc()) {
        $item_name = $item['Pavadinimas'];
        $item_price = $item['Kaina'];
        $item_discount = $item['Nuolaida'];
        $discounted_price = $item['DiscountedPrice'];
        $item_quantity = $item['Kiekis'];
        $item_total = $discounted_price * $item_quantity;

        $grand_total += $item_total;

        echo "<tr>
                <td>{$item_name}</td>
                <td>{$item_price} €</td>
                <td>{$item_discount}%</td>
                <td>{$discounted_price} €</td>
                <td>{$item_quantity}</td>
                <td>{$item_total} €</td>
              </tr>";
    }

    echo "<tr><td colspan='5' style='text-align:right;'>Bendra suma:</td><td>{$grand_total} €</td></tr>";
    echo "</table>";
} else {
    echo "<p>Šis užsakymas neturi prekių.</p>";
}

$conn->close();
?>
