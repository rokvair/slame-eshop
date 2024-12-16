<?php
session_start();
include 'header.php';  // Including the header
include 'config.php';  // Database connection

// Admin authentication check (optional)
/*if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo "<p>Access denied. Only administrators can view this page.</p>";
    exit;
}*/

// Check if order_id is provided in the query string
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    echo "<p>Nenurodytas užsakymo ID.</p>";
    exit;
}

$order_id = intval($_GET['order_id']);
$conn = connectDB();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_status'])) {
    $new_status = $_POST['new_status'];

    // Update the order status in the database
    $sql_update = "UPDATE uzsakymas SET Statusas = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("si", $new_status, $order_id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Užsakymo statusas atnaujintas į '{$new_status}'.</p>";
    } else {
        echo "<p style='color: red;'>Nepavyko atnaujinti užsakymo statuso.</p>";
    }
}

// Fetch order and user details
$sql_order = "SELECT u.Data AS order_date, u.Statusas AS order_status,
                     n.Vardas AS user_name, n.Pavarde AS user_surname, n.El_pastas AS user_email
              FROM uzsakymas u
              JOIN naudotojas n ON u.fk_Naudotojas = n.id
              WHERE u.id = ?";
$stmt = $conn->prepare($sql_order);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    echo "<p>Užsakymas nerastas.</p>";
    exit;
}

$order = $order_result->fetch_assoc();
$order_date = $order['order_date'];
$order_status = $order['order_status'];
$user_name = $order['user_name'];
$user_surname = $order['user_surname'];
$user_email = $order['user_email'];

echo "<h1>Užsakymo detalės (ID: {$order_id})</h1>";
echo "<p>Data: {$order_date}</p>";
echo "<p>Statusas: {$order_status}</p>";
echo "<p>Naudotojas: {$user_name} {$user_surname} ({$user_email})</p>";

// Display status change form
echo "<form method='POST'>
        <label for='new_status'>Keisti užsakymo būseną:</label>
        <select name='new_status' id='new_status'>
            <option value='Laukiantis patvirtinimo'" . ($order_status === 'Laukiantis patvirtinimo' ? ' selected' : '') . ">Laukiantis patvirtinimo</option>
            <option value='Patvirtintas'" . ($order_status === 'Patvirtintas' ? ' selected' : '') . ">Patvirtintas</option>
            <option value='Išsiųstas'" . ($order_status === 'Issiustas' ? ' selected' : '') . ">Išsiųstas</option>
            <option value='Atmestas'" . ($order_status === 'Atmestas' ? ' selected' : '') . ">Atmestas</option>
            <option value='Pristatytas'" . ($order_status === 'Pristatytas' ? ' selected' : '') . ">Pristatytas</option>
        </select>
        <button type='submit'>Atnaujinti</button>
      </form>";

// Fetch order items
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
    echo "<h2>Užsakymo prekės</h2>";
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
