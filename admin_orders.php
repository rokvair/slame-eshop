<?php
session_start();
include 'header.php';  // Including the header
include 'config.php';  // Database connection

// Admin authentication check (optional)


$conn = connectDB();

// Fetch all orders and their user details
$sql = "SELECT u.id AS order_id, u.Data AS order_date, u.Statusas AS order_status,
               n.Vardas AS user_name, n.Pavarde AS user_surname, n.El_pastas AS user_email,
               SUM(p.Kaina * up.Kiekis) AS total_price
        FROM uzsakymas u
        JOIN naudotojas n ON u.fk_Naudotojas = n.id
        JOIN uzsakymo_preke up ON u.id = up.fk_Uzsakymas
        JOIN preke p ON up.fk_Preke = p.id
        GROUP BY u.id, u.Data, u.Statusas, n.Vardas, n.Pavarde, n.El_pastas
        ORDER BY u.id DESC";  // Orders sorted by ID (most recent first)
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Display all orders
echo "<h1>Visi užsakymai</h1>";

if ($result->num_rows > 0) {
    echo "<table border='1'>
          <tr>
            <th>Užsakymo ID</th>
            <th>Data</th>
            <th>Statusas</th>
            <th>Naudotojo vardas</th>
            <th>Naudotojo pavardė</th>
            <th>El. paštas</th>
            <th>Bendra suma</th>
            <th>Veiksmas</th>
          </tr>";

    while ($order = $result->fetch_assoc()) {
        $order_id = $order['order_id'];
        $order_date = $order['order_date'];
        $order_status = $order['order_status'];
        $user_name = $order['user_name'];
        $user_surname = $order['user_surname'];
        $user_email = $order['user_email'];
        $total_price = $order['total_price'];

        echo "<tr>
                <td>{$order_id}</td>
                <td>{$order_date}</td>
                <td>{$order_status}</td>
                <td>{$user_name}</td>
                <td>{$user_surname}</td>
                <td>{$user_email}</td>
                <td>{$total_price} €</td>
                <td><a href='admin_order_details.php?order_id={$order_id}'>Peržiūrėti detales</a></td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nėra užsakymų.</p>";
}

$conn->close();
?>
