<?php
session_start();
include 'header.php';  // Including the header
include 'config.php';  // Database connection

// Assuming user_id is stored in session
$user_id = $_SESSION['user_id'];  // Replace with actual user authentication
$conn = connectDB();

// Fetch user's purchase history
$sql = "SELECT u.id, u.Data, u.Statusas, SUM(p.Kaina * up.Kiekis) AS TotalPrice
        FROM uzsakymas u
        JOIN uzsakymo_preke up ON u.id = up.fk_Uzsakymas
        JOIN preke p ON up.fk_Preke = p.id
        WHERE u.fk_Naudotojas = ?
        GROUP BY u.id, u.Data, u.Statusas
        ORDER BY u.Data DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Display the purchase history
echo "<h1>Jūsų užsakymų istorija</h1>";
if ($result->num_rows > 0) {
    echo "<table border='1'>
          <tr>
            <th>Užsakymo ID</th>
            <th>Data</th>
            <th>Būsena</th>
            <th>Bendra suma</th>
            <th>Veiksmas</th>
          </tr>";

    while ($order = $result->fetch_assoc()) {
        $order_id = $order['id'];
        $order_date = $order['Data'];
        $order_status = $order['Statusas'];
        $total_price = $order['TotalPrice'];

        echo "<tr>
                <td>{$order_id}</td>
                <td>{$order_date}</td>
                <td>{$order_status}</td>
                <td>{$total_price} €</td>
                <td><a href='order_details.php?order_id={$order_id}'>Peržiūrėti detaliau</a></td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Neturite užsakymų istorijos.</p>";
}

$conn->close();
?>
